<?php
App::uses('File', 'Utility');
App::import('Vendor','Zebra_Image',array('file' => 'ZebraImage.class.php'));

class Picture {

    /**
     * Resize and/or crop an image
     *
     * @param $path
     * @param int $width
     * @param int $height
     * @param null $action
     * @return mixed
     * @throws InternalErrorException
     */
    public function resizeCrop($path, $width = 0, $height = 0, $action = ZEBRA_IMAGE_CROP_TOPCENTER){
        ini_set("memory_limit", "10000M");

        # Flag
        $delete_png = false;
        $image = new Zebra_Image();
        # Load image
        $image->source_path = $path;
        # The target will be the same image
        $target = $path;
        # Get File Extension
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        # Convert PNG files to JPG if configured on bootstrap.php
        if (in_array($ext, array("png", "jpeg", 'tif','jp2', 'jpx', 'j2k', 'j2c', 'tiff','tiff','gif','jpeg', 'jif', 'jfif','fpx','pcd'))) {
            # Flag to check must delete the png file
            $delete_png = true;
            # Store PNG file path to delete later
            $png_file = $target;
            # Update target path with JPG extension
            $target = str_replace(array(".png", ".jpeg", '.tif','.jp2', '.jpx', '.j2k', '.j2c', '.tiff','.tiff','.gif','.jpeg', '.jif', '.jfif','.fpx','.pcd'), '.jpg', $path);
        }
        # The target will be the same image
        $image->target_path = $target;
        # JPG quality
        $image->jpeg_quality = 45;
        # Extra configs
        $image->preserve_aspect_ratio  = true;
        $image->enlarge_smaller_images = true;
        $image->preserve_time = true;

        if (!$image->resize($width, $height, $action)) {
            // if there was an error, let's see what the error is about
            switch ($image->error) {
                case 1:
                    throw new InternalErrorException('Source file could not be found!');
                    break;
                case 2:
                    throw new InternalErrorException('Source file is not readable!');
                    break;
                case 3:
                    throw new InternalErrorException('Could not write target file!');
                    break;
                case 4:
                    throw new InternalErrorException('Unsupported source file format!');
                    break;
                case 5:
                    throw new InternalErrorException('Unsupported target file format!');
                    break;
                case 6:
                    throw new InternalErrorException('GD library version does not support target file format!');
                    break;
                case 7:
                    throw new InternalErrorException('GD library is not installed!');
                    break;
                case 8:
                    throw new InternalErrorException('"chmod" command is disabled via configuration!');
                    break;
            }
        } else {
            # Delete PNG file if needed
            if ($delete_png) {
                unlink($png_file);
            }

            return $target;
        }
    } 
}

?>