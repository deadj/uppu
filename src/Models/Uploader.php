<?php

class Uploader 
{
	private string $savePath;
	private getID3 $getID3;
	private Helper $helper;
	private GearmanClient $gearmanClient;
	private SphinxSearch $sphinxSearch;
	private FilesTable $filesTable;

	public function __construct(
		Helper $helper,
		GearmanClient $gearmanClient,
		SphinxSearch $sphinxSearch,
		FilesTable $filesTable
	){
		$this->savePath = 'files/' . date("m_d_y");
		$this->getID3 = new getID3;
		$this->helper = $helper;
		$this->gearmanClient = $gearmanClient;
		$this->sphinxSearch = $sphinxSearch;
		$this->filesTable = $filesTable;
	}

	public function uploadFile(
		string $fileLink, 
		string $fileName, 
		string $userName, 
		string $comment
	): string
	{
        if (!file_exists($this->savePath)) {
            mkdir($this->savePath);
        }

		$extension = pathinfo($fileName, PATHINFO_EXTENSION);
		$nameId = $this->saveFile($this->savePath . "/", $fileLink, $extension);
		$link = $this->createFilesLink($this->savePath, $nameId, $extension);
		$type = $this->getFileType($link);

        if ($type == File::TYPE_VIDEO) {
            $fileData = $this->getID3->analyze($link);

            if (!isset($fileData['video']['fourcc_lookup']) || 
                !preg_match('/H[.]264/iu', $fileData['video']['fourcc_lookup'])) {
                $this->gearmanClient->addServer();
                $res = $this->gearmanClient->doBackground('convertVideo', $link);

                $metadata = MediaInfo::getNullMetadataForVideo();
                $size = 0;
                $uploadIsDone = File::STATUS_NULL;
            } else {
                $metadata = MediaInfo::getMetadata($type, $link);
                $size = MediaInfo::getSize($link);
                $uploadIsDone = File::STATUS_DONE;
            }

            $link = preg_replace('/[.]\\w*/', '.mp4', $link);
        } else {
            $this->helper->createImagePreview($link, $nameId);
            $metadata = MediaInfo::getMetadata($type, $link);
            $size = MediaInfo::getSize($link);
            $uploadIsDone = File::STATUS_DONE;
        }

        if ($extension == "php" || $extension == "phtml") {
            $link = preg_replace('/[.](php|phtml)$/', '.txt', $link);

        }

        $comment = trim(mb_substr(strval($comment), 0, 30));
        $date = date("Y-m-d H:i:s");
        
        $file = new File(NULL, $nameId, $userName, $link, $comment, $type, $date, $size, $metadata, $uploadIsDone);
        $fileId = $this->filesTable->addFile($file);
        $this->sphinxSearch->add($fileId, $file);

        return $nameId;
	}

    private function createFilesLink(string $folderPath, string $nameId, string $extension): string
    {
        return $folderPath . "/" . $nameId . '.' . $extension;
    }

    private function saveFile(
    	string $directory, 
        $file, 
        string $extension
    ): string
    {
        if ($extension == "php" || $extension == "phtml") {
            $extension = "txt";
        }
        
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $tusClient = new \TusPhp\Tus\Client('http://tus.com/TusServer.php');
        $tusClient->setApiPath('http://tus.com/TusServer.php');

        $uploadKey = uniqid();
        $tusClient->setKey($uploadKey)->file($file, $filename);

        $fileSize = $tusClient->getFileSize();

        while ($tusClient->getOffset() != $fileSize) {
            $tusClient->upload(5000000);
        }

        return preg_replace('/[.].*/', '', $filename);
    }

    private function getFileType(string $link): string
    {
    	$type = $this->getID3->analyze($link)['mime_type'];

        if (preg_match('/video/', $type)) {
            return File::TYPE_VIDEO;
        } elseif (preg_match('/image/', $type)) {
            return File::TYPE_IMAGE;
        } elseif (preg_match('/audio/', $type)) {
            return File::TYPE_AUDIO;
        } else {
            return File::TYPE_OTHER;
        }   
    }

}