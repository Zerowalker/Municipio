
@grid(['container' => true,"columns" => "4","max_width" => "350px", 'row_gap' => 6])
    @foreach($posts as $post)
    
        @card([
                'href' => $post->permalink,
                'imageFirst' => true,
                'image' =>  ['src' => $post->featuredimage['src'], 'alt' => 'featured image'],
                'heading' => $post->postTitle,
                'classList' => ['archive-card'],
                'byline' => ['text' => $post->postDate, 'position' => 'body'],
                'content' => $post->excerpt,
                'buttons' => [['text' => 'Go', 'href' => $post->permalink]],
            ])
        @endcard
    
    @endforeach
@endgrid
