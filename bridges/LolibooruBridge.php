<?php
class LolibooruBridge extends BridgeAbstract{

	const MAINTAINER = "mitsukarenai";
	const NAME = "Lolibooru";
	const URI = "http://lolibooru.moe/";
	const DESCRIPTION = "Returns images from given page and tags";

    const PARAMETERS = array( array(
        'p'=>array(
            'name'=>'page',
            'defaultValue'=>1,
            'type'=>'number'
        ),
        't'=>array('name'=>'tags')
    ));

    public function collectData(){
        $html = $this->getSimpleHTMLDOM(
            self::URI.'post?'
            .'&page='.$this->getInput('p')
            .'&tags='.urlencode($this->getInput('t'))
        ) or $this->returnServerError('Could not request Lolibooru.');

	$input_json = explode('Post.register(', $html);
	foreach($input_json as $element)
	 $data[] = preg_replace('/}\)(.*)/', '}', $element);
	unset($data[0]);

        foreach($data as $datai) {
	    $json = json_decode($datai, TRUE);
            $item = array();
            $item['uri'] = self::URI.'post/show/'.$json['id'];
            $item['postid'] = $json['id'];
            $item['timestamp'] = $json['created_at'];
            $item['imageUri'] = $json['file_url'];
            $item['title'] = 'Lolibooru | '.$json['id'];
            $item['content'] = '<a href="' . $item['imageUri'] . '"><img src="' . $json['preview_url'] . '" /></a><br>Tags: '.$json['tags'];
            $this->items[] = $item;
        }
    }

    public function getCacheDuration(){
        return 1800; // 30 minutes
    }
}
