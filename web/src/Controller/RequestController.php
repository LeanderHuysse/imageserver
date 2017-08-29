<?php
namespace IOLabs\Controller;

use IOLabs\Helper\SourceHelper as SourceHelper;
use IOLabs\Handler\ImageHandler as ImageHandler;

class RequestController
{
    private $src = null;

    private $requestType = null;

    /**
     * @param array $requestData
     *
     * @return bool
     */
    public function validateRequest($requestData = []): bool
    {
        if(array_key_exists('id', $requestData)) {
            $src = (int) $requestData['id'];
            $this->setSrc($src);

            if(array_key_exists('type', $requestData)) {
                $this->setRequestType((string) $requestData['type']);
            }

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function srcExists(): bool
    {
        $sourceHelper = new SourceHelper();
        if($sourceHelper->sourceExists($this->getSrc())) {
            return true;
        }

        return false;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function serveImage($data = [])
    {
        $imageHandler = new ImageHandler();
        $sourceHelper = new SourceHelper();
        $images = $sourceHelper->getSources($this->getSrc());

        if($data['w'] > 0 || $data['h'] >= 0) {
            if($this->getRequestType() === 'single') {
                // Image needs to be resized
                $image = $imageHandler->resizeImage($images[0], $data['w'], $data['h']);
                header("Content-type: image/jpeg");
                echo $image->getImageBlob();
                exit();
            } elseif ($this->getRequestType() === 'gallery') {
                $imagesArray = [];
                foreach($images as $img) {
                    $image = $imageHandler->resizeImage($img, $data['w'], $data['h']);
                    $imagesArray[] = $image->getImageBlob();
                }
                echo json_encode($imagesArray);
                exit();
            }
        }
    }

    /**
     * @return null
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * @param null $src
     */
    public function setSrc($src)
    {
        $this->src = $src;
    }

    /**
     * @return null
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * @param null $requestType
     */
    public function setRequestType($requestType)
    {
        $this->requestType = $requestType;
    }

}