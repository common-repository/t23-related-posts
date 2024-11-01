<?php

ini_set('display_errors', 'on');

require_once __DIR__ . '/default.php';

class T23RelatedPostsFrontend extends T23RelatedPosts
{
    public $error;
    public $warning;

    protected function init()
    {
        if ($this->getOption('enabled')) {
            add_filter(
                'the_content',
                array($this, 'contentHook'),
                10
            );
        }
    }

    public function contentHook($content)
    {
        if (get_post_type() === 'post' && is_single()) {
            $posts = $this->getRelatedPosts(get_the_ID());

            if ($posts || $this->error || $this->warning) {
                $content .= $this->buildPostHtml($posts);
            }
        }

        return $content;
    }


    /**
     * @param $postId
     * @return array[WP_Post]
     */
    public function getRelatedPosts($postId)
    {
        $post = get_post($postId);
        $keywords = $this->getKeywordsFromPost($post);
        $ids = $this->searchPostIds($keywords, $postId);

        if (!$ids) {
            return array();
        }

        return get_posts(array('post__in' => $ids, 'orderby' => 'post__in'));
    }

    protected function searchPostIds($keywords, $postId)
    {
        $q = $this->flattenWords($keywords);

        $sphinx = new \SphinxClient();
        $sphinx->SetServer($this->getOption('server'), $this->getOption('port'));
        $sphinx->SetMatchMode(SPH_MATCH_EXTENDED2);
        $sphinx->SetSortMode(SPH_SORT_RELEVANCE);
        $sphinx->SetFieldWeights($this->getOption('weights'));

        $sphinx->setLimits(0, $this->getOption('result_limit'), 500);

        $result = $sphinx->query($q, $this->getQueryIndex());

        if ($sphinx->getLastError()) {
            $this->error = $sphinx->getLastError();
        }

        if ($sphinx->getLastWarning()) {
            $this->warning = $sphinx->getLastWarning();
        }

        if ($result && isset($result['matches'])) {

            $ids = array();

            foreach ($result['matches'] as $id => $d) {
                if ($id == $postId) {
                    continue;
                }

                if ($d['weight'] >= $this->getOption('min_weight')) {
                    $ids[] = $id;
                }
            }

            if ($ids) {
                return $ids;
            }
        }

        return array();
    }

    protected function flattenWords(array $words)
    {
        $out = array_map(
            function ($el) {
                return htmlspecialchars($el, ENT_QUOTES, 'utf-8');
            },
            $words
        );

        return implode(" | ", $out);
    }

    protected function getKeywordsFromPost(WP_Post $post, $limit = 20)
    {
        $str = $post->post_title;

        $categories = $this->getPostTermNames($post->ID, 'category');

        foreach ($categories as $category) {
            $str .= ' ' . $category;
        }

        $tags = $this->getPostTermNames($post->ID, 'tag');

        foreach ($tags as $tag) {
            $str .= ' ' . $tag;
        }

        $wordList = explode(' ', $str);

        foreach ($wordList as $k => $word) {
            $word = mb_strtolower($word);
            $word = preg_replace('/[^a-zа-я0-9\s]/ui', '', $word);
            if (mb_strlen($word) <= 2) {
                unset($wordList[$k]);
                continue;
            }

            $wordList[$k] = $word;
        }

        $a = array_count_values($wordList);

        foreach ($this->getStopWords() as $word) {
            unset($a[$word]);
        }

        arsort($a, SORT_NUMERIC);

        $outWords = array_slice($a, 0, min(count($a), $limit));

        return array_keys($outWords);
    }

    private function getPostTermNames($postId, $taxonomy)
    {
        $tags = wp_get_post_terms($postId, $taxonomy);

        $names = array();

        foreach ($tags as $tag) {
            $names[] = $tag->name;
        }

        return $names;
    }

    protected function buildPostHtml($posts)
    {
        if (!$posts) {
            $posts = array();
        }

        $out = '';

        $out .= '<div class="t23-related">';
        $out .= '<div class="t23-related-title">' . $this->getOption('title') . '</div>';

        if ($this->error || $this->warning) {
            if (is_user_logged_in() && current_user_can('manage_options')) {
                if ($this->error) {
                    $out .= '<div class="t23-error"><strong>' . __('Error') . '</strong>: ' . $this->error . '</div>';
                }

                if ($this->warning) {
                    $out .= '<div class="t23-warning"><strong>' . __(
                            'Warning'
                        ) . '</strong>: ' . $this->error . '</div>';
                }
            }
        }

        $out .= '<ol>';
        foreach ($posts as $post) {
            $out .= '<li>';
            $attr = htmlspecialchars_decode($this->getOption('link_attr', ''));
            $out .= '<a href="' . get_permalink($post->ID) . '" ' . $attr . ' >' . get_the_title($post->ID) . '</a>';
            $out .= '</li>';
        }
        $out .= '</ol>';
        $out .= '</div>';

        return $out;
    }

    protected function getStopWords()
    {
        $words = $this->getOption('stop_words');
        if (!$words) {
            return array();
        }

        $words = explode(',', $words);
        $words = array_map(
            function ($word) {
                return trim($word);
            },
            $words
        );

        return $words;
    }
}