<?php
namespace App;

class GoPublic extends \Illuminate\Foundation\Application
{
 /**
 * Get the path to the public / web directory.
 *
 * @return string
 */
 public function publicPath()
 {
    //return $this->basePath.DIRECTORY_SEPARATOR.'../../public_html/nnewidio';
    return '/home/hanjorsc/public_html/nnewidio';
 }
}