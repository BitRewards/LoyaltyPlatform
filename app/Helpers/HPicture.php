<?php

class HPicture
{
    const BASE_PATH = '/var/www/uploads/';

    public static function getLocalFilenameByUploadUrl($url)
    {
        $filename = HMisc::getFilename($url);

        return self::getUploadPath($filename);
    }

    public static function getUploadUrlByLocalFilename($path)
    {
        $filename = HMisc::getFilename($path);

        return self::getUploadUrl($filename);
    }

    public static function imageCreate($filename)
    {
        if (0 === strpos($filename, Yii::app()->params['uploadsBaseUrl'])) {
            $filename = HMisc::getFilename($filename);
        }

        if (false === strpos($filename, '/')) {
            $filename = self::getUploadPath($filename);
        }

        $extension = HMisc::getExtension($filename);

        switch ($extension) {
            case 'png':
                $result = @imagecreatefrompng($filename);

                break;

            case 'jpg':
            case 'jpeg':
                $result = @imagecreatefromjpeg($filename);

                break;

            case 'gif':
                $result = @imagecreatefromgif($filename);

                break;

            default:
                throw new CException('Unknown image format');
        }

        return $result;
    }

    public static function imageSave($resource, $path)
    {
        $extension = HMisc::getExtension($path);

        switch ($extension) {
            case 'png':
                $result = imagepng($resource, $path);

                break;

            case 'jpg':
            case 'jpeg':
                $result = imagejpeg($resource, $path, 85);

                break;

            case 'gif':
                $result = imagegif($resource, $path);

                break;

            default:
                throw new CException('Unknown image format');
        }

        if (!$result) {
            throw new CException("Failed to save image to path $path");
        }

        return $result;
    }

    public static function generateUploadFilename($extension = 'jpg')
    {
        $count = 0;

        do {
            $filename = HMisc::generateRandomString(16).'.'.$extension;
        } while (file_exists(self::BASE_PATH.$filename) && $count < 10);

        if (10 == $count) {
            throw new CException("Unable to generate new picture filename, last tried $filename");
        }

        return $filename;
    }

    public static function glueCard($filenameLeft, $filenameRight)
    {
        $left = self::imageCreate($filenameLeft);
        $right = self::imageCreate($filenameRight);

        $heightLeft = imagesy($left);
        $heightRight = imagesy($right);
        $heightTotal = max($heightLeft, $heightRight);
        $widthLeft = imagesx($left);
        $widthRight = imagesx($right);

        $widthLeftDesired = round($widthLeft * $heightTotal / $heightLeft);
        $widthRightDesired = round($widthRight * $heightTotal / $heightRight);

        $totalWidth = $widthRightDesired + $widthLeftDesired;

        $result = imagecreatetruecolor($totalWidth, $heightTotal);

        imagecopyresampled($result, $left, 0, 0, 0, 0, $widthLeftDesired, $heightTotal, $widthLeft, $heightLeft);
        imagecopyresampled($result, $right, $widthLeftDesired, 0, 0, 0, $widthRightDesired, $heightTotal, $widthRight, $heightRight);

        $filenameGlued = self::generateUploadFilename();

        self::imageSave($result, self::getUploadPath($filenameGlued));

        imagedestroy($left);
        imagedestroy($right);
        imagedestroy($result);

        return $filenameGlued;
    }

    public static function splitCard($filenameSource)
    {
        $filenameLeft = HMisc::appendToFilename(HMisc::getFilename($filenameSource), '_left');
        $filenameRight = HMisc::appendToFilename(HMisc::getFilename($filenameSource), '_right');

        $filenameLeftFull = self::getUploadPath($filenameLeft);
        $filenameRightFull = self::getUploadPath($filenameRight);

        if (file_exists($filenameLeftFull) && file_exists($filenameRightFull)) {
            return [$filenameLeft, $filenameRight];
        }

        $source = self::imageCreate($filenameSource);

        if (!$source) {
            return [$filenameLeft, $filenameRight];
        }

        $height = imagesy($source);
        $widthRight = $height;
        $widthLeft = imagesx($source) - $widthRight;

        $left = imagecreatetruecolor($widthLeft, $height);
        $right = imagecreatetruecolor($widthRight, $height);
        imagecopyresampled($left, $source, 0, 0, 0, 0, $widthLeft, $height, $widthLeft, $height);
        imagecopyresampled($right, $source, 0, 0, $widthLeft, 0, $widthRight, $height, $widthRight, $height);

        self::imageSave($left, $filenameLeftFull);
        self::imageSave($right, $filenameRightFull);

        imagedestroy($left);
        imagedestroy($right);
        imagedestroy($source);

        return [$filenameLeft, $filenameRight];
    }

    public static function compress($input, $output)
    {
        HMisc::echoIfInConsole("\nStarting compression of $input...\n");

        $keys = Yii::app()->params['tinypng']['apiKeys'];
        $key = $keys[mt_rand(0, count($keys) - 1)];

        $sourceContents = file_get_contents($input);
        $inputSize = filesize($input);

        $request = curl_init();
        curl_setopt_array($request, array(
            CURLOPT_URL => 'https://api.tinypng.com/shrink',
            CURLOPT_USERPWD => 'api:'.$key,
            CURLOPT_POSTFIELDS => $sourceContents,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_TIMEOUT => 15,
            /* Uncomment below if you have trouble validating our SSL certificate.
               Download cacert.pem from: http://curl.haxx.se/ca/cacert.pem */
            // CURLOPT_CAINFO => __DIR__ . "/cacert.pem",
            CURLOPT_SSL_VERIFYPEER => false,
        ));

        $response = curl_exec($request);

        if (201 === curl_getinfo($request, CURLINFO_HTTP_CODE)) {
            /* Compression was successful, retrieve output from Location header. */
            $headers = substr($response, 0, curl_getinfo($request, CURLINFO_HEADER_SIZE));

            foreach (explode("\r\n", $headers) as $header) {
                if ('Location: ' === substr($header, 0, 10)) {
                    $request = curl_init();
                    curl_setopt_array($request, array(
                        CURLOPT_URL => substr($header, 10),
                        CURLOPT_RETURNTRANSFER => true,
                        /* Uncomment below if you have trouble validating our SSL certificate. */
                        // CURLOPT_CAINFO => __DIR__ . "/cacert.pem",
                        CURLOPT_SSL_VERIFYPEER => true,
                    ));
                    $result = curl_exec($request);

                    if ($result && (strlen($result) > 100)) {
                        file_put_contents($output, curl_exec($request));
                    } else {
                        return false;
                    }
                }
            }
        } else {
            HMisc::echoIfInConsole(curl_error($request));
            /* Something went wrong! */
            HMisc::echoIfInConsole("\n\n\nCompression failed!\n\n\n");

            return false;
        }

        if (file_exists($output) && filesize($output) > 50) {
            clearstatcache();
            HMisc::echoIfInConsole("\nSuccessfully compressed $input from ".$inputSize.' bytes to '.filesize($output)." bytes\n");

            return true;
        } else {
            HMisc::echoIfInConsole("\n\nCompressing $input failed for some reason \n\n");

            return false;
        }
    }

    public static function optimizeFile($path, $disablePngToJpeg = true)
    {
        $originalPath = $path;
        $slashParts = explode('/', $path);
        $file = end($slashParts);
        $folder = str_replace($file, '', $path);

        $parts = explode('.', $file);
        $extension = $parts[count($parts) - 1];

        if ('jpg' != $extension && 'png' != $extension) {
            return $path;
        }

        if ('jpg' == $extension) {
            exec("jpegoptim --quiet --max=85 --strip-all $path");

            return $path;
        }

        $image = HPicture::imageCreate($path);

        if (!$disablePngToJpeg && 'png' == $extension) {
            $colorsCount = exec("identify -format '%k' '$path'");

            if ($colorsCount > 255) {
                $extension = 'jpg';
                array_pop($parts);
                $parts[] = $extension;
                $file = implode('.', $parts);
                $path = $folder.$file;
                imagejpeg($image, $path);
            }
        }

        imagedestroy($image);

        if (HPicture::compress($path, $folder.$file)) {
            return $path;
        } else {
            return $originalPath;
        }
    }

    public static function getUploadPath($filename)
    {
        return self::BASE_PATH.$filename;
    }

    public static function getUploadUrl($filename)
    {
        return Yii::app()->params['uploadsBaseUrl'].'/'.$filename;
    }

    public static function getFakeFriends($blurred = false)
    {
        return $blurred ? (include __DIR__.'/views/fake-friends-blurred.php') : (include __DIR__.'/views/fake-friends-no-blur.php');
    }

    public static function getBlankAva($letter = null, $default = 'lightblue')
    {
        $blankPath = "/social/img/ava_blank_{$default}.png";

        if (!$letter) {
            return Yii::app()->controller->getStaticRoot().$blankPath;
        }
        $colorsCount = 10;
        $ord = HMisc::utf8Ord(mb_strtolower($letter));

        $filename = APPLICATION_PATH.'/frontend/www'.($path = ('/social/img/blank_avas/'.$ord.'_'.mt_rand(0, $colorsCount - 1).'.png'));

        if (file_exists($filename)) {
            return Yii::app()->controller->getStaticRoot().$path;
        } else {
            return Yii::app()->controller->getStaticRoot().$blankPath;
        }
    }

    public static function hexToRgba($hexColor, $opacity = 1.0)
    {
        $hexColor = ltrim($hexColor, '#');

        return
            'rgba('.
            hexdec(substr($hexColor, 0, 2)).', '.
            hexdec(substr($hexColor, 2, 2)).', '.
            hexdec(substr($hexColor, 4, 2)).', '.
            $opacity.
            ')';
    }

    public static function isColorDarkEnough($hexColor)
    {
        list($h, $s, $l) = self::rgb2hsl($hexColor);

        return $l <= 0.75;
    }

    public static function desaturate($hexColor, $saturation = 0.7, $lightness = 0.97)
    {
        list($h, $s, $l) = self::rgb2hsl($hexColor);

        $s = $saturation;
        $l = $lightness;
        list($r, $g, $b) = self::hsl2rgb($h, $s, $l);

        $result = self::makeCssColor($r, $g, $b);

        return $result;
    }

    public static function makeCssColor($r, $g, $b)
    {
        return '#'.str_pad(dechex($r), 2, '0', STR_PAD_LEFT).str_pad(dechex($g), 2, '0', STR_PAD_LEFT).str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }

    public static function lighter($hexColor, $increase = 20)
    {
        $hexColor = trim($hexColor, '# ');
        $r = max(min(hexdec(substr($hexColor, 0, 2)) + $increase, 255), 0);
        $g = max(min(hexdec(substr($hexColor, 2, 2)) + $increase, 255), 0);
        $b = max(min(hexdec(substr($hexColor, 4, 2)) + $increase, 255), 0);

        return '#'.
            str_pad(dechex($r), 2, '0', STR_PAD_LEFT).
            str_pad(dechex($g), 2, '0', STR_PAD_LEFT).
            str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }

    public static function darker($hexColor, $decrease = 20)
    {
        return self::lighter($hexColor, -$decrease);
    }

    public static function getPathByUploadUrl($url)
    {
        $filename = HMisc::getFilename($url);

        $path = self::getUploadPath($filename);

        if (!file_exists($path)) {
            $context = null;

            if (!HMisc::isProduction()) {
                $context = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
            }

            $contents = @file_get_contents($url, false, $context);

            if (!$contents) {
                return null;
            }
            $extension = HMisc::getExtension($url);
            $path = self::getUploadPath(HMisc::generateRandomString(12).'.'.$extension);
            file_put_contents($path, $contents);
        }

        return $path;
    }

    public static function isUploadUrl($url)
    {
        return false === strpos($url, '/uploads/') ? false : true;
    }

    public static function resize($pictureUrl, $newWidth, $newHeight)
    {
        $path = self::getPathByUploadUrl($pictureUrl);

        if (!$path) {
            return $pictureUrl;
        }

        list($width, $height, $type, $attr) = @getimagesize($path);

        if ($width == $newWidth && $height == $newHeight) {
            return $pictureUrl;
        }

        $image = self::imageCreate($path);

        $new_image = imagecreatetruecolor($newWidth, $newHeight);

        imagecopyresampled($new_image, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $result = self::generateUploadFilename(HMisc::getExtension($path));

        self::imageSave($new_image, $newPath = self::getUploadPath($result));

        return self::getUploadUrl($result);
    }

    public static function resizeIfSmaller($pictureUrl, $newWidth, $newHeight)
    {
        $path = self::getPathByUploadUrl($pictureUrl);

        list($width, $height, $type, $attr) = @getimagesize($path);

        if ($width < $newHeight || $height < $newHeight) {
            return $pictureUrl;
        }

        $image = self::imageCreate($path);

        $new_image = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($new_image, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $result = self::generateUploadFilename();

        self::imageSave($new_image, $newPath = self::getUploadPath($result));

        return self::getUploadUrl($result);
    }

    public static function rgb2hsl($color)
    {
        $color = trim($color, '# ');

        if (3 == strlen($color)) {
            $color = $color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
        }

        $r = hexdec(substr($color, 0, 2));
        $g = hexdec(substr($color, 2, 2));
        $b = hexdec(substr($color, 4, 2));

        $oldR = $r;
        $oldG = $g;
        $oldB = $b;
        $r /= 255;
        $g /= 255;
        $b /= 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        $d = $max - $min;

        if (0 == $d) {
            $h = $s = 0; // achromatic
        } else {
            $s = $d / (1 - abs(2 * $l - 1));

            switch ($max) {
                case $r:
                    $h = 60 * fmod((($g - $b) / $d), 6);

                    if ($b > $g) {
                        $h += 360;
                    }

                    break;

                case $g:
                    $h = 60 * (($b - $r) / $d + 2);

                    break;

                case $b:
                    $h = 60 * (($r - $g) / $d + 4);

                    break;
            }
        }

        return array(round($h, 2), round($s, 2), round($l, 2));
    }

    public static function hsl2rgb($h, $s, $l)
    {
        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod(($h / 60), 2) - 1));
        $m = $l - ($c / 2);

        if ($h < 60) {
            $r = $c;
            $g = $x;
            $b = 0;
        } elseif ($h < 120) {
            $r = $x;
            $g = $c;
            $b = 0;
        } elseif ($h < 180) {
            $r = 0;
            $g = $c;
            $b = $x;
        } elseif ($h < 240) {
            $r = 0;
            $g = $x;
            $b = $c;
        } elseif ($h < 300) {
            $r = $x;
            $g = 0;
            $b = $c;
        } else {
            $r = $c;
            $g = 0;
            $b = $x;
        }
        $r = ($r + $m) * 255;
        $g = ($g + $m) * 255;
        $b = ($b + $m) * 255;

        return array(floor($r), floor($g), floor($b));
    }

    public static function roundCorners($sourceImageFile, $radius)
    {
        // test source image
        if (file_exists($sourceImageFile)) {
            $res = is_array($info = getimagesize($sourceImageFile));
        } else {
            $res = false;
        }

        // open image
        if ($res) {
            $w = $info[0];
            $h = $info[1];

            switch ($info['mime']) {
                case 'image/jpeg': $src = imagecreatefromjpeg($sourceImageFile);

                    break;

                case 'image/gif': $src = imagecreatefromgif($sourceImageFile);

                    break;

                case 'image/png': $src = imagecreatefrompng($sourceImageFile);

                    break;

                default:
                    $res = false;
            }
        }

        // create corners
        if ($res) {
            $q = 10; // change this if you want
            $radius *= $q;

            // find unique color
            do {
                $r = rand(0, 255);
                $g = rand(0, 255);
                $b = rand(0, 255);
            } while (imagecolorexact($src, $r, $g, $b) < 0);

            $nw = $w * $q;
            $nh = $h * $q;

            $img = imagecreatetruecolor($nw, $nh);
            $alphacolor = imagecolorallocatealpha($img, $r, $g, $b, 127);
            imagealphablending($img, false);
            imagesavealpha($img, true);
            imagefilledrectangle($img, 0, 0, $nw, $nh, $alphacolor);

            imagefill($img, 0, 0, $alphacolor);
            imagecopyresampled($img, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

            imagearc($img, $radius - 1, $radius - 1, $radius * 2, $radius * 2, 180, 270, $alphacolor);
            imagefilltoborder($img, 0, 0, $alphacolor, $alphacolor);
            imagearc($img, $nw - $radius, $radius - 1, $radius * 2, $radius * 2, 270, 0, $alphacolor);
            imagefilltoborder($img, $nw - 1, 0, $alphacolor, $alphacolor);
            imagearc($img, $radius - 1, $nh - $radius, $radius * 2, $radius * 2, 90, 180, $alphacolor);
            imagefilltoborder($img, 0, $nh - 1, $alphacolor, $alphacolor);
            imagearc($img, $nw - $radius, $nh - $radius, $radius * 2, $radius * 2, 0, 90, $alphacolor);
            imagefilltoborder($img, $nw - 1, $nh - 1, $alphacolor, $alphacolor);
            imagealphablending($img, true);
            imagecolortransparent($img, $alphacolor);

            // resize image down
            $dest = imagecreatetruecolor($w, $h);
            imagealphablending($dest, false);
            imagesavealpha($dest, true);
            imagefilledrectangle($dest, 0, 0, $w, $h, $alphacolor);
            imagecopyresampled($dest, $img, 0, 0, 0, 0, $w, $h, $nw, $nh);

            // output image
            $res = $dest;
            imagedestroy($src);
            imagedestroy($img);
        }

        return $res;
    }

    public static function getCardPictureRounded(Card $card)
    {
        $path = self::getPathByUploadUrl($card->picture);
        list($width, $height) = @getimagesize($path);

        if (!$width) {
            return null;
        }

        if ($card->partner->has_rectangle_cards) {
            $radius = round(0.042253521 * $width);
        } else {
            $radius = round(0.2 * $width);
        }
        $gd = self::roundCorners($path, $radius);

        if (!$gd) {
            return null;
        }

        $filename = HMisc::changeExtension(
            HMisc::appendToFilename($path, '_rounded'),
            'png'
        );

        self::imageSave($gd, $filename);

        self::optimizeFile($filename);

        return self::getUploadUrlByLocalFilename($filename);
    }

    /**
     * @param $url
     * @param $options
     *
     * @return string
     *
     * See https://github.com/willnorris/imageproxy for options manual
     */
    public static function getResizedUrl($url, $options)
    {
        if (false !== strpos($url, '.local') || false !== strpos($url, '.tech-local')) {
            return $url;
        }

        if ('/' == $url[0] && '/' == $url[1]) {
            $url = 'https:'.$url;
        }

        return 'https://resizer.giftd.tech/'.$options.'/'.str_replace('static.', '', $url);
    }
}
