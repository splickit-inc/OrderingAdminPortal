<?php namespace App\Service;

class Image {

    public $file;
    public $file_width;
    public $file_height;
    public $file_extension;

    public $crop = [];
    public $file_crop = [];
    public $result_image_dimensions = [];

    public $new_height;
    public $new_width;

    public function __construct($file_image) {
        $this->file = $file_image;
        $this->getFileImageWidthHeight();
        $this->file_extension = $this->getImageExtension();
    }

    public function getImageExtension() {
        $jpg_count = substr_count(strtoupper($this->file), '.JPG');
        $jpeg_count = substr_count(strtoupper($this->file), '.JPEG');
        $png_count = substr_count(strtoupper($this->file), '.PNG');
        $gif_count = substr_count(strtoupper($this->file), '.GIF');

        if ($jpg_count > 0 || $jpeg_count > 0) { $extension = "jpg"; }
        elseif ($png_count > 0) { $extension = "png"; }
        elseif ($gif_count > 0) { $extension = "gif"; }
        return $extension;
    }

    public function getFileImageWidthHeight() {
        list($file_image_width, $file_image_height) = getimagesize($this->file);
        $this->file_width = $file_image_width;
        $this->file_height = $file_image_height;
    }

    public function getCropValuesRelativeToFileImage() {
        $this->file_crop['x'] = round(($this->crop['crop_x']/$this->crop['canvas_width'])*$this->file_width);
        $this->file_crop['y'] = round(($this->crop['crop_y']/$this->crop['canvas_height'])*$this->file_height);

        $this->file_crop['width'] = round(($this->crop['crop_width']/$this->crop['canvas_width'])*$this->file_width);
        $this->file_crop['height'] = round(($this->crop['crop_height']/$this->crop['canvas_height'])*$this->file_height);
    }

    public function createCroppedImage() {
        if ($this->file_extension == 'jpg') {
            $image = \imagecreatefromjpeg($this->file);
        }
        elseif ($this->file_extension == 'gif') {
            $image = \imagecreatefromgif($this->file);
        }
        else {
            $image = \imagecreatefrompng($this->file);
        }

        $image_p = \imagecreatetruecolor($this->result_image_dimensions['width'], $this->result_image_dimensions['height']);

        \imagecopyresampled($image_p, $image, 0, 0,
            $this->file_crop['x'], $this->file_crop['y'],
            $this->result_image_dimensions['width'], $this->result_image_dimensions['height'],
            $this->file_crop['width'], $this->file_crop['height']);

        return $image_p;
    }

    public function newWidthHeight($image_config) {

        if (isset($image_config['set_width'])) {
            $this->new_width = $image_config['set_width'];
            $this->new_height = $image_config['set_height'];
        }
        else {
            $new_max_length = $image_config['max_width_height'];

            if ($this->file_width > $this->file_height) {
                $this->new_width = $new_max_length;
                $this->new_height = round($new_max_length*($this->file_height/$this->file_width));
            }
            elseif ($this->file_height > $this->file_width) {
                $this->new_height = $new_max_length;
                $this->new_width = round($new_max_length*($this->file_width/$this->file_height));
            }
            else {
                $this->new_width = $new_max_length;
                $this->new_height = $new_max_length;
            }
        }
    }

    public function createImageObject($tmp_image_location) {
        if ($this->file_extension == 'jpg') {
            $image = \imagecreatefromjpeg($this->file);
        }
        elseif ($this->file_extension == 'gif') {
            $image = \imagecreatefromgif($this->file);
        }
        else {
            $image = \imagecreatefrompng($this->file);
        }

        $image_p = \imagecreatetruecolor($this->new_width, $this->new_height);

        \imagealphablending( $image_p, false );
        \imagesavealpha( $image_p, true );

        \imagecopyresampled($image_p, $image, 0, 0,
            0, 0,
            $this->new_width, $this->new_height,
            $this->file_width, $this->file_height);

        $extension = $this->getImageExtension($tmp_image_location);

        if ($extension == 'jpg') {
            \imagejpeg($image_p, $tmp_image_location);
        }
        elseif ($extension == 'png') {

            \imagepng($image_p, $tmp_image_location);
        }
        else {
            \imagegif($image_p, $tmp_image_location);
        }

        // Create new imagick object
        $imagic_image = new \Imagick($tmp_image_location);

        // Optimize the image layers
        $imagic_image->optimizeImageLayers();

        // Compression and quality
        if ($extension == 'jpg') {
            $imagic_image->setImageCompression(\Imagick::COMPRESSION_JPEG);
            $imagic_image->setImageCompressionQuality(25);

            // Write the image back
            $imagic_image->writeImages($tmp_image_location, true);
        }
        elseif ($extension == 'png') {
            $imagic_image->setImageFormat('png');
            $imagic_image->setImageCompressionQuality(25);

            // Write the image back
            $imagic_image->writeImages($tmp_image_location, true);
        }
        else {
            $imagic_image->setImageFormat('gif');
            $imagic_image->setImageCompressionQuality(25);

            // Write the image back
            $imagic_image->writeImages($tmp_image_location, true);
        }
    }
}
