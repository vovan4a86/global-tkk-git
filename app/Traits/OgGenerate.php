<?php namespace App\Traits;
use Illuminate\Support\Str;
use Image;
use OpenGraph;
use Settings;
use Thumb;

/**
 * Created by PhpStorm.
 * User: aleks
 * Date: 19.12.2017
 * Time: 11:09
 */


trait OgGenerate{
	public function ogGenerate() {
		OpenGraph::setUrl($this->url);
        if ($this->og_title) {
            OpenGraph::setTitle($this->og_title);
        } else {
            OpenGraph::setTitle($this->title);
        }
        if ($this->og_description) {
            OpenGraph::setDescription($this->og_description);
        } else {
            OpenGraph::setDescription($this->description);
        }
        if ($this->image) {
            OpenGraph::addImage($this->image_src);
        } else {
            OpenGraph::addImage('/static/images/favicon/apple-touch-icon.png');
        }
	}
}