<?php

namespace NickDeKruijk\ImageResize;

use App\Http\Controllers\Controller;
use Cache;
use App;

class ResizeController extends Controller
{
    private static function error($message = 'Undefined error')
    {
        if (App::runningInConsole()) {
            dd($message);
        } elseif (config('app.debug')) {
            throw new \Exception($message);
        } else {
            abort(404);
        }
    }

    private static function path($append = null)
    {
        return public_path(rtrim(config('imageresize.route'), '/') . ($append ? '/' . $append : ''));
    }

    public static function delete($template = null)
    {
        if ($template && !config('imageresize.templates.' . $template)) {
            self::error('Template ' . $template . ' not found');
        }

        if ($template) {
            self::deleteDir(self::path($template));
        } else {
            foreach (config('imageresize.templates') as $template => $array) {
                self::deleteDir(self::path($template));
            }
        }
    }

    private static function deleteDir($dir)
    {
        if (!file_exists($dir) || !is_dir($dir)) return false;
        $h = opendir($dir);
        while ($f = readdir($h)) {
            if ($f[0] != '.')
                if (is_dir($dir . '/' . $f))
                    self::deleteDir($dir . '/' . $f);
                else {
                    echo 'Deleting ' . $dir . '/' . $f . chr(10);
                    unlink($dir . '/' . $f);
                }
        }
        echo 'Deleting dir ' . $dir . chr(10);
        rmdir($dir);
        closedir($h);
    }

    private function makepath($target)
    {
        $dir = explode('/', $target);
        array_pop($dir);
        $p = '';
        foreach ($dir as $d) {
            $p .= $d . '/';
            if (!file_exists($p)) {
                mkdir($p) or $this->error('Unable to create ' . $p);
            }
            if (!is_dir($p)) {
                $this->error('Not a directory: ' . $p);
            }
        }
        if (!is_writable($p)) {
            $this->error('Not writable: ' . $p);
        }
    }

    public function resize($template, $original, $target)
    {
        $originalSize = getimagesize($original) or $this->error($original . ' is not a valid image');
        $type = $originalSize['mime'];
        $originalWidth = $originalSize[0];
        $originalHeigth = $originalSize[1];

        # Create new GD instance based on image type
        if ($type == 'image/gif') {
            $image_a = imagecreatefromgif($original) or $this->error();
        } elseif ($type == 'image/png') {
            $image_a = imagecreatefrompng($original) or $this->error();
        } else {
            $image_a = imagecreatefromjpeg($original) or $this->error();
        }

        if ($template['type'] == 'crop') {
            $targetWidth = $template['width'];
            $targetHeigth = $originalHeigth * ($targetWidth / $originalWidth);
            if ($targetHeigth < $template['height']) {
                $targetHeigth = $template['height'];
                $targetWidth = $originalWidth * ($targetHeigth / $originalHeigth);
            }
            $dst_img = imagecreatetruecolor($targetWidth, $targetHeigth);
            imagealphablending($dst_img, false);
            imagesavealpha($dst_img, true);
            imagecopyresampled($dst_img, $image_a, 0, 0, 0, 0, $targetWidth, $targetHeigth, imagesx($image_a), imagesy($image_a));
            imagedestroy($image_a);
            $image_p = imagecreatetruecolor($template['width'], $template['height']);
            imagealphablending($image_p, false);
            imagesavealpha($image_p, true);
            imagecopy($image_p, $dst_img, 0, 0, round((imagesx($dst_img) - $template['width']) / 2), round((imagesy($dst_img) - $template['height']) / 2), $template['width'], $template['height']);
            imagedestroy($dst_img);
        } elseif ($template['type'] == 'fit') {
            $ratio_orig = $originalWidth / $originalHeigth;
            if ($template['width'] / $template['height'] > $ratio_orig) {
                $template['width'] = $template['height'] * $ratio_orig;
            } else {
                $template['height'] = $template['width'] / $ratio_orig;
            }
            if (($template['width'] > $originalWidth || $template['height'] > $originalHeigth) && (!isset($template['upscale']) || $template['upscale'] === false)) {
                # Don't upscale image just copy it
                $image_p = imagecreatetruecolor($originalWidth, $originalHeigth);
                imagecopy($image_p, $image_a, 0, 0, 0, 0, $originalWidth, $originalHeigth);
            } else {
                $image_p = imagecreatetruecolor($template['width'], $template['height']);
                imagealphablending($image_p, false);
                imagesavealpha($image_p, true);
                imagecopyresampled($image_p, $image_a, 0, 0, 0, 0, $template['width'], $template['height'], $originalWidth, $originalHeigth);
            }
            imagedestroy($image_a);
        } else {
            $this->error('Invalid template type ' . $template['type']);
        }

        # Add blur filter if needed
        if (isset($template['blur']) && $template['blur'] > 0) {
            for ($x = 1; $x <= $template['blur']; $x++) {
                imagefilter($image_p, IMG_FILTER_GAUSSIAN_BLUR);
            }
        }

        # Add grayscale filter if needed
        if (isset($template['grayscale']) && $template['grayscale']) {
            imagefilter($image_p, IMG_FILTER_GRAYSCALE);
        }

        $this->makepath($target);

        # Change type to force output format if used in template
        if (isset($template['output'])) {
            if (in_array($template['output'], ['jpg', 'jpeg', 'png', 'gif'])) {
                $type = 'image/' . $template['output'];
            } else {
                self::error('Invalid output ' . $template['output']);
            }
        }

        # Save the resized image in a variable
        if ($type == 'image/gif') {
            imagegif($image_p, $target) or $this->error('Write error');
        } elseif ($type == 'image/png') {
            imagepng($image_p, $target) or $this->error('Write error');
        } else {
            imagejpeg($image_p, $target, isset($template['quality']) ? $template['quality'] : config('imageresize.quality_jpeg')) or $this->error('Write error');
        }

        imagedestroy($image_p);
    }

    public function make($template, $image)
    {
        if (config('imageresize.templates.' . $template)) {

            $target = rtrim(config('imageresize.route'), '/') . '/' . $template . '/' . $image;
            $template = config('imageresize.templates.' . $template);

            $original = rtrim(config('imageresize.originals'), '/') . '/' . $image;
            if (!file_exists($original)) {
                $this->error($original . ' does not exist');
            }
            if (is_dir($original)) {
                $this->error($original . ' is a directory');
            }

            $this->resize($template, $original, $target);

            return redirect($target);
        } else {
            $this->error('Template ' . $template . ' not found');
        }
    }
}
