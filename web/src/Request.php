<?php

class Request
{
    private $data;

    private $type;

    private $directory;

    private $file;

    /**
     * Request constructor.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->directory = dirname(__DIR__) . '/data';

        if($this->verify($data)) {
            $this->setData($data);
            $this->setType($data['type']);
            $this->get();
        }

    }

    /**
     * @param $data
     *
     * @return bool
     */
    protected function verify($data): bool
    {
        if(!array_key_exists('id', $data)) {
            return false;
        }

        if(!array_key_exists('w', $data)) {
            return false;
        }

        if(!array_key_exists('type', $data)) {
            return false;
        }

        return true;
    }

    public function check(): bool
    {
        $id = (string) $this->getData()['id'];

        $result = glob($this->directory . '/' . $id . '*');
        if(count($result) >  0) {
            return true;
        }

        return false;
    }

    public function serve()
    {
        $file = $this->getFile();
        $type = $this->getType();

        if($type == 'featured') {
            header("Content-type: image/jpeg");
            echo $this->resize();
        } elseif($type == 'gallery') {
            $imageArray = [];
            foreach($file as $img) {
                $imageArray[] = $this->resizeArray($img);
            }
            echo json_encode($imageArray);
            die();
        }
    }

    protected function resize()
    {
        $width = $this->getData()['w'];
        $height = isset($this->getData()['h']) ? $this->getData()['h'] : 0;

        $image = new \Imagick($this->getFile());
        $image->scaleImage($width, $height);

        return $image;

    }

    protected function resizeArray($file)
    {
        $width = $this->getData()['w'];
        $height = isset($this->getData()['h']) ? $this->getData()['h'] : 0;

        $image = new \Imagick($file);
        $image->scaleImage($width, $height);

        return $image->getImageBlob();

    }

    protected function get()
    {
        $id = (string) $this->getData()['id'];
        $type = $this->getType();

        if($type === 'featured') {
            $result = glob($this->directory . '/' . $id . '_x.jpg');
            if(count($result) < 1) {
                $result = glob($this->directory . '/' . $id . '.jpg');
            }

            if(count($result) > 0) {
                $this->setFile($result[0]);
                return true;
            }
            return false;
        } elseif($type == 'gallery') {
            $extraThumb = [];
            $featured = glob($this->directory . '/' . $id . '_x.jpg');

            if(count($featured) < 1) {
                $featured = glob($this->directory . '/' . $id . '.jpg');
            } else {
                $extraThumb = glob($this->directory . '/' . $id . '.jpg');
            }

            $result = glob($this->directory . '/' . $id . '_[0-9]{[0-9],}.jpg', GLOB_BRACE);

            if(count($result) > 0) {
                array_unshift($result, $extraThumb[0]);
                asort($result);
                $this->setFile($result);
            }

        }
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

}