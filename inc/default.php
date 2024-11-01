<?php

abstract class T23RelatedPosts
{
    const OPTIONS_GROUP = 't23-related-posts';
    const I18N_DOMAIN = 't23-related-posts';

    protected $version = '0.0.1';
    protected $options;

    protected $pluginDir;
    protected $pluginName;
    protected $pluginFile;
    protected $templateDir;

    public function __construct()
    {
        $this->pluginDir = realpath(__DIR__ . '/../');
        $this->templateDir = $this->pluginDir . '/templates';
        $this->pluginName = basename($this->pluginDir);
        $this->pluginFile = $this->pluginDir . '/' . $this->pluginName . '.php';

        $this->options = $this->getSavedOptions();
        $this->init();
    }

    abstract protected function init();

    protected function getSavedOptions()
    {
        if ($options = get_option(self::OPTIONS_GROUP)) {
            return $options;
        }

        return $this->getDefaults();
    }

    protected function getOption($option, $default = null)
    {
        if (false !== strpos($option, ':')) {
            $keys = explode(':', $option);

            $data = $this->options;

            foreach ($keys as $key) {

                if (!isset($data[$key])) {
                    return $default;
                }

                $data = $data[$key];
            }

            return $data;
        }

        return isset ($this->options[$option]) ? $this->options[$option] : $default;
    }

    protected function overrideOptions($options)
    {
        $db_options = $this->options;
        if (!is_array($db_options) || !is_array($options)) {
            return false;
        }
        $this->options = array_merge($db_options, $options);

        return true;
    }

    protected function getDefaults()
    {
        return array(
            'enabled' => false,
            'version' => $this->version,
            'link_attr' => '',
            'server' => 'localhost',
            'port' => 9312,
            'weights' => array(
                'title' => 40,
                'text' => 5,
                'categories' => 30,
                'tags' => 20,
            ),
            'result_limit' => 5,
            'title' => __('Related Posts', self::OPTIONS_GROUP),
            'min_weight' => 50,
            'prefix' => 't23-',
            'stop_words' => 'a, an, the, and, of, i, to, is, in, with, for, as, that, on, at, this, my, was, our, it, you, we, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 10, about, after, all, almost, along, also, amp, another, any, are, area, around, available, back, be, because, been, being, best, better, big, bit, both, but, by, c, came, can, capable, control, could, course, d, dan, day, decided, did, didn, different, div, do, doesn, don, down, drive, e, each, easily, easy, edition, end, enough, even, every, example, few, find, first, found, from, get, go, going, good, got, gt, had, hard, has, have, he, her, here, how, if, into, isn, just, know, last, left, li, like, little, ll, long, look, lot, lt, m, made, make, many, mb, me, menu, might, mm, more, most, much, name, nbsp, need, new, no, not, now, number, off, old, one, only, or, original, other, out, over, part, place, point, pretty, probably, problem, put, quite, quot, r, re, really, results, right, s, same, saw, see, set, several, she, sherree, should, since, size, small, so, some, something, special, still, stuff, such, sure, system, t, take, than, their, them, then, there, these, they, thing, things, think, those, though, through, time, today, together, too, took, two, up, us, use, used, using, ve, very, want, way, well, went, were, what, when, where, which, while, white, who, will, would, your, а, в, Я, это, алтухов, быть, вот, вы, да, еще, и, как, мы, не, нет, о, они, от, с, сказать, только, у, этот, большой, в, все, говорить, для, же, из, который, на, него, них, один, оно, ото, свой, та, тот, что, я, бы, весь, всей, год, до, знать, к, мочь, наш, нее, но, она, оный, по, себя, такой, ты, это ',
        );
    }

    public function getQueryIndex()
    {
        return $this->getOption('prefix').'ix-related-content';
    }
}