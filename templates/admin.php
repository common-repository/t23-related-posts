<?php
if (!class_exists('T23RelatedPosts')) {
    exit;
}

$p = T23RelatedPosts::OPTIONS_GROUP;

?>
<div class="wrap t23">
    <div id="icon-options-general" class="icon32"><br></div>

    <h2>T23 Related Posts Plugin</h2>

    <form action="options.php" method="post">
        <?php echo settings_fields(T23RelatedPosts::OPTIONS_GROUP) ?>

        <h3><?php echo _e('General Settings', T23RelatedPosts::I18N_DOMAIN) ?></h3>

        <table class="form-table form-table-clearnone">

            <tr valign="top">
                <th scope="row"><?php echo _e('Enabled:', T23RelatedPosts::I18N_DOMAIN) ?></th>
                <td>
                    <input type="checkbox" value="on" <?php echo $this->getOption(
                        'enabled'
                    ) ? 'checked="checked"' : '' ?>  id="blog_public" name="<?php echo $p ?>[enabled]">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php echo _e('Search server:', T23RelatedPosts::I18N_DOMAIN) ?></th>
                <td>
                    <input class="regular-text code" type="text"
                           value="<?php echo $this->getOption('server') ?>" name="<?php echo $p ?>[server]">
                    <label for="searchserver_port"><?php echo _e('Port:', T23RelatedPosts::I18N_DOMAIN) ?></label>
                    <input id="searchserver_port" class="small-text" type="text"
                           value="<?php echo $this->getOption('port') ?>" name="<?php echo $p ?>[port]">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php echo _e('Title of HTML block:', T23RelatedPosts::I18N_DOMAIN) ?></th>
                <td>
                    <input class="regular-text code" type="text" value="<?php echo $this->getOption('title') ?>"
                           name="<?php echo $p ?>[title]">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php echo _e('Max result posts:', T23RelatedPosts::I18N_DOMAIN) ?></th>
                <td>
                    <input class="regular-text code" type="text" value="<?php echo $this->getOption('result_limit') ?>"
                           name="<?php echo $p ?>[result_limit]">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php echo _e('Attr HTML link:', T23RelatedPosts::I18N_DOMAIN) ?></th>
                <td>
                    <input class="regular-text code" type="text" value="<?php echo htmlspecialchars($this->getOption('link_attr')) ?>"
                           name="<?php echo $p ?>[link_attr]">
                </td>
            </tr>
        </table>

        <p class="submit">
            <input class="button button-primary" type="submit"
                   value="<?php echo _e('Save settings', T23RelatedPosts::I18N_DOMAIN) ?>" name="submit">
        </p>

        <h3><?php echo _e('Search settings', T23RelatedPosts::I18N_DOMAIN) ?></h3>

        <p>
            <?php echo _e(
                'Post title, categories, tags, text are used to search similar content. All occurrence has weigth.<br>' .
                'The larger sum weigths, the greater the similarity.<br>' .
                'Publications which have a weight of less than the minimum are not similar',
                T23RelatedPosts::I18N_DOMAIN
            ) ?>
        </p>

        <table class="form-table form-table-clearnone">

            <tr valign="top">
                <th scope="row"><?php echo _e('Min weight:', T23RelatedPosts::I18N_DOMAIN) ?></th>
                <td>
                    <input class="regular-text code" type="text" value="<?php echo $this->getOption('min_weight') ?>"
                           name="<?php echo $p ?>[min_weight]">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php echo _e('Weight occurrence in TITLE:', T23RelatedPosts::I18N_DOMAIN) ?></th>
                <td>
                    <input class="regular-text code" type="text" value="<?php echo $this->getOption('weights:title') ?>"
                           name="<?php echo $p ?>[weights][title]">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo _e('Weight occurrence in CATEGORY:', T23RelatedPosts::I18N_DOMAIN) ?></th>
                <td>
                    <input class="regular-text code" type="text"
                           value="<?php echo $this->getOption('weights:categories') ?>"
                           name="<?php echo $p ?>[weights][categories]">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo _e('Weight occurrence in TAG:', T23RelatedPosts::I18N_DOMAIN) ?></th>
                <td>
                    <input class="regular-text code" type="text" value="<?php echo $this->getOption('weights:tags') ?>"
                           name="<?php echo $p ?>[weights][tags]">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php echo _e('Weight occurrence in TEXT:', T23RelatedPosts::I18N_DOMAIN) ?></th>
                <td>
                    <input class="regular-text code" type="text" value="<?php echo $this->getOption('weights:text') ?>"
                           name="<?php echo $p ?>[weights][text]">
                </td>
            </tr>

        </table>

        <p class="submit">
            <input class="button button-primary" type="submit"
                   value="<?php echo _e('Save settings', T23RelatedPosts::I18N_DOMAIN) ?>" name="submit">
        </p>

        <h3><?php echo _e('Stop words', T23RelatedPosts::I18N_DOMAIN) ?></h3>

        <p><?php echo _e(
                'Stop words that are not used in calculating the posts keywords that are used to determine similarity.',
                T23RelatedPosts::I18N_DOMAIN
            ) ?></p>
        <textarea class="large-text code" rows="8" name="<?php echo $p ?>[stop_words]"><?php echo $this->getOption(
                'stop_words'
            ) ?></textarea>

        <p class="submit">
            <input class="button button-primary" type="submit"
                   value="<?php echo _e('Save settings', T23RelatedPosts::I18N_DOMAIN) ?>" name="submit">
        </p>

        <h3><?php echo _e('Sphinx conf file', T23RelatedPosts::I18N_DOMAIN) ?></h3>

        <p><?php echo _e(
                'Below is a sample Sphinx configuration file. You need to configure the search server manually.',
                T23RelatedPosts::I18N_DOMAIN
            ) ?></p>
        <table class="form-table form-table-clearnone">
            <tr valign="top">
                <th scope="row"><?php echo _e('Prefix:', T23RelatedPosts::I18N_DOMAIN) ?></th>
                <td>
                    <input class="regular-text code" type="text" value="<?php echo $this->getOption('prefix') ?>"
                           name="<?php echo $p ?>[prefix]">
                </td>
            </tr>
        </table>

        <p class="submit">
            <input class="button button-primary" type="submit"
                   value="<?php echo _e('Save settings', T23RelatedPosts::I18N_DOMAIN) ?>" name="submit">
        </p>
    </form>

    <div class="sphinx-conf">
        <pre><?php echo $this->generateSphinxConfigFile() ?></pre>
    </div>
</div>