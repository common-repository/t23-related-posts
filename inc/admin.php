<?php

require_once __DIR__ . '/default.php';


class T23RelatedPostsAdmin extends T23RelatedPosts
{
    protected function init()
    {
        register_activation_hook(
            $this->pluginFile,
            array($this, 'activateHook')
        );

        add_action(
            'admin_menu',
            array($this, 'addPage')
        );

        add_action(
            'admin_init',
            array($this, 'registerSetting')
        );

        load_plugin_textdomain($this->pluginName, false, $this->pluginName . '/languages');
    }

    public function activateHook()
    {
        if (!extension_loaded('sphinx')) {
            deactivate_plugins(basename($this->pluginDir));
            wp_die(
                'You must install Sphinx PHP extension before active plugin ' .
                '(<a target="_blank" href="http://www.php.net/manual/en/book.sphinx.php">documentation</a>)'
            );
        }
    }

    public function addPage()
    {
        if (current_user_can('manage_options')) {
            $options_page = add_options_page(
                __('Related Posts', self::OPTIONS_GROUP),
                __('Related Posts', self::OPTIONS_GROUP),
                'manage_options',
                self::OPTIONS_GROUP,
                array($this, 'adminPage')
            );

            add_action(
                'admin_print_styles-' . $options_page,
                array($this, 'css')
            );
        }
    }

    public function css()
    {
        wp_register_style(
            self::OPTIONS_GROUP,
            plugins_url(basename($this->pluginDir) . '/css/admin.css'),
            null,
            $this->version
        );
        wp_enqueue_style(self::OPTIONS_GROUP);
    }

    public function adminPage()
    {
        require $this->templateDir . '/admin.php';
    }

    public function registerSetting()
    {
        register_setting(
            self::OPTIONS_GROUP,
            self::OPTIONS_GROUP,
            array($this, 'sanitizeOptions')
        );
    }

    public function sanitizeOptions($data)
    {
        $defaults = $this->getDefaults();
        $data = array_merge($defaults, $data);

        $bool = array(
            'enabled',
        );

        foreach ($bool as $var) {
            if ($data[$var] == 'on') {
                $data[$var] = true;
            } else {
                $data[$var] = false;
            }
        }

        $int = array(
            'port',
            'result_limit',
            'result_cutoff',
            'min_weight',
        );

        foreach ($int as $i) {
            $data[$i] = intval($data[$i]);
            $data[$i] = max($data[$i], 0);
        }

        if (isset($data['weights']) && is_array($data['weights'])) {
            foreach ($data['weights'] as $k => $v) {
                $data['weights'][$k] = max(intval($v), 0);
            }
        }

        return $data;
    }

    public function generateSphinxConfigFile()
    {
        global $wpdb;

        $data = require $this->templateDir . '/sphinx.conf.php';

        $data = strtr(
            $data,
            array(
                '{prefix}' => $this->getOption('prefix'),
                '{DB_HOST}' => DB_HOST,
                '{DB_USER}' => DB_USER,
                '{DB_PASSWORD}' => 'hidden',
                '{DB_NAME}' => DB_NAME,
                '{wp_posts}' => $wpdb->prefix . 'posts',
                '{wp_term_relationships}' => $wpdb->prefix . 'term_relationships',
                '{wp_term_taxonomy}' => $wpdb->prefix . 'term_taxonomy',
                '{wp_terms}' => $wpdb->prefix . 'terms',
                '{sphinx_path}' => '/var/lib/sphinx',
                '{server}' => $this->getOption('server'),
                '{port}' => $this->getOption('port'),
            )
        );

        return $data;
    }
}