<?php

/**
 * Class Request
 */
class Request
{
    /**
     * @var
     */
    private $data;

    /**
     * @var
     */
    private $type;

    /**
     * @var string
     */
    private $directory;

    /**
     * @var
     */
    private $file;

    /**
     * @var
     */
    private $id;

    /**
     * Request constructor.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->directory = dirname(__DIR__) . '/data';
        $this->write = dirname(__DIR__) . '/images';

        if($this->verify($data)) {
            $this->setData($data);
            $this->setType($data['type']);
            $this->setId($data['id']);
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

    /**
     * @return bool
     */
    public function check(): bool
    {
        $id = (string) $this->getData()['id'];
        $type = (string) $this->getType();

        if($type !== 'tile' && $type !== 'header') {
            $result = glob($this->directory . '/' . $id . '*');
            if(count($result) >  0) {
                return true;
            }
        } else {
            $result = glob($this->directory . '/categories/' . $id . '*');
            if(count($result) >  0) {
                return true;
            }
        }

        return false;
    }

    /**
     * test 2
     */
    public function serve()
    {
        $file = $this->getFile();
        $type = $this->getType();

        if($type == 'featured') {
            header("Content-type: image/jpeg");
            echo $this->resize();
        } elseif($type == 'gallery') {
            $imageArray = [];
            $count = 0;

            /**
             * Make directory if it doesn't exist yet
             */
            if(!file_exists($this->write . '/' . $this->getId())) {
                $oldumask = umask(0);
                mkdir($this->write . '/' . $this->getId(), 0777);
                umask($oldumask);
            }

            $cache_life = 1800;

            foreach($file as $img) {
                $t = array_pop(explode('/', $img));
                $tname = $this->write . '/' . $this->getId() .'/'. $t . '_300.jpg';
                $filemtime = filemtime($tname);

                if(!file_exists($tname) or (time() - $filemtime >= $cache_life)
                ) {
                    $thumbnail = $this->resizeArray($img);
                    if($f = fopen($tname, "w")) {
                        $thumbnail->writeImageFile($f);
                    }
                }

                $l = array_pop(explode('/', $img));
                $lname = $this->write . '/' . $this->getId() .'/'. $l . '_800_lb.jpg';

                if(!file_exists($lname) or (time() - $filemtime >= $cache_life)) {
                    $lightbox = $this->resizeArray($img, 800);
                    if ($f = fopen($lname, "w")) {
                        $lightbox->writeImageFile($f);
                    }
                }

                $imageArray[$count]['thumb'] = $this->getRelative(array_pop(explode('/', $tname)), $this->getId());
                $imageArray[$count]['lightbox'] = $this->getRelative(array_pop(explode('/', $lname)), $this->getId());

                $count++;
            }

            echo json_encode($imageArray, true);
            die();
        } elseif($type == 'tile') {
            header("Content-type: image/jpeg");
            echo $this->resize();
        } elseif($type == 'header') {
            header("Content-type: image/jpeg");
            echo $this->resize();
        }
    }

    /**
     * @param $file
     * @param $id
     *
     * @return string
     */
    public function getRelative($file, $id)
    {
        $base = 'https://cdn.iolabs.nl/images/'. $id .'/'. $file;
        return $base;
    }

    /**
     * @return Imagick
     */
    protected function resize()
    {
        $width = $this->getData()['w'];
        $height = isset($this->getData()['h']) ? $this->getData()['h'] : 0;

        $image = new \Imagick($this->getFile());
        $image->scaleImage($width, $height);

        return $image;

    }

    /**
     * @param     $file
     * @param int $w
     *
     * @return Imagick
     */
    public function resizeArray($file, $w = 0)
    {
        if($w !== 0) {
            $width = $w;
        } else {
            $width = $this->getData()['w'];
        }
        $height = isset($this->getData()['h']) ? $this->getData()['h'] : 0;

        $image = new \Imagick($file);
        $image->resizeImage($width, $height, \Imagick::FILTER_CATROM, 1);

        return $image;

    }

    /**
     * @return bool
     */
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

            if(count($featured) > 0) {
                $extraThumb = glob($this->directory . '/' . $id . '.jpg');
            }

            $result = glob($this->directory . '/' . $id . '_[0-9]{[0-9],}.jpg', GLOB_BRACE);

            if(count($result) > 0) {
                if(count($extraThumb) > 0) {
                    array_unshift($result, $extraThumb[0]);
                }
                natsort($result);
                $this->setFile($result);
            } elseif(count($extraThumb) > 0) {
                $this->setFile($extraThumb);
            }

        } elseif($type == 'tile') {
            $result = glob($this->directory . '/categories/' . $id . '_tegel-a.jpg');
            if(count($result) > 0) {
                $this->setFile($result[0]);
                return true;
            }
        } elseif($type == 'header') {
            $result = glob($this->directory . '/categories/' . $id . '_header-a.jpg');
            if(count($result) > 0) {
                $this->setFile($result[0]);
                return true;
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

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }



}
