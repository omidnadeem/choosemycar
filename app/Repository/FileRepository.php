<?php

namespace App\Repository;

use Symfony\Component\HttpFoundation\File\File as FileObj;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Illuminate\Support\Arr;

class FileRepository
{

    /**
     * @var array supported file extensions
     */
    public static $fileExtensions = ['json', 'xml'];

    protected  $fileName;
    protected  $fileContent;
    const JSON = 'json';
    const XML = 'xml';

    /**
     *
     * @param string $filePath
     * @throws FileNotFoundException If the given path is not a file
     */
    public function readFile(string $filePath = null)
    {
        if (empty($filePath)) {
            throw new FileNotFoundException($filePath);
        }
        $file = new FileObj($filePath);
        $this->setData($file);
        return $this;
    }
    
    /**
     *
     * @param FileObj $fileObject
     * @return void
     */
    protected function setData(FileObj $fileObject): void
    {
        $this->setFileName($fileObject->getFilename());
        $this->setFileContent($fileObject->getContent());
    }
    /**
     * 
     * @param string $fileContent
     * @return void
     */
    protected function setFileContent(string $fileContent): void
    {
        $this->fileContent = $fileContent;
    }


    /**
     *
     * @return array
     */
    public function getFileContent()
    {
        if (!$this->isSupported()) {
            throw new \Exception('.' . $this->getExtension() . ' file is not supported! Supported formats:' . implode('|', static::$fileExtensions));
        }

        if ($this->isJson()) {
            return $this->processJsonToArray($this->fileContent);
        }

        return $this->processXmlToArray($this->fileContent);
    }

    /**
     *
     * @param string $jsonFile
     * @return array
     */
    public function processJsonToArray(string $jsonFile): array
    {
        $json = json_decode($jsonFile, TRUE);
        return $json;
    }

    /**
     *
     * @param string $xmlFile
     * @return array
     */
    public function processXmlToArray(string $xmlFile): array
    {
        $xml = simplexml_load_string($xmlFile);
        $array = json_decode(json_encode((array)$xml), TRUE);
        return Arr::first($array);
    }

    /** 
     * @param string $fileName
     * @return void
     */
    protected function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }
    /**
     * Returns the file name without path
     * @return string
     */
    public function getFilename(): string
    {
        return $this->fileName;
    }

    /**
     * Returns the file extension.
     * @return string
     */
    public function getExtension(): string
    {
        return \File::extension($this->fileName);
    }

    /**
     * Checks if the file extension is supported.
     * @return boolean
     */
    public function isSupported(): bool
    {
        return in_array(strtolower($this->getExtension()), static::$fileExtensions);
    }

    /**
     * check if the file extension is json
     * @return boolean
     */
    public function isJson(): bool
    {
        return ($this->getExtension() === static::JSON);
    }
    /**
     * check if the file extension is xml
     * @return boolean
     */
    public function isXml(): bool
    {
        return ($this->getExtension() === static::XML);
    }
}
