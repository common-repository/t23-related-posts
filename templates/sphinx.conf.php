<?php
return <<<EOF
source {prefix}so-related-content{
        type            = mysql
        sql_host        = {DB_HOST}
        sql_user        = {DB_USER}
        sql_pass        = {DB_PASSWORD}
        sql_db          = {DB_NAME}
        sql_port        = 3306

        sql_query_pre   = SET NAMES utf8
        sql_query_range = SELECT MIN(id),MAX(id) FROM {wp_posts} WHERE post_status = 'publish' AND post_type = 'post'
        sql_range_step  = 500

        sql_query       = select \
                p.ID as post_ID,\
                p.post_title as title, \
                p.post_content as text, \
                LOWER(GROUP_CONCAT(IF(tt.taxonomy = 'category', t.name, null))) as categories, \
                LOWER(GROUP_CONCAT(IF(tt.taxonomy = 'post_tag', t.name, null))) as tags, \
                t.name as categories, \
                UNIX_TIMESTAMP(post_date) AS date_added \
        from \
                {wp_posts} as p \
        left join \
                {wp_term_relationships} tr on (p.ID = tr.object_id) \
        left join \
                {wp_term_taxonomy} tt on (tt.term_taxonomy_id = tr.term_taxonomy_id and tt.taxonomy IN ('category', 'post_tag')) \
        left join \
                {wp_terms} t on (tt.term_id = t.term_id) \
        where \
                p.id>=\$start AND p.id<=\$end and \
                p.post_status = 'publish' and \
                p.post_type = 'post'
        group by p.ID

        sql_attr_timestamp      = date_added
}

index {prefix}ix-related-content{
        source          = {prefix}so-related-content
        path            = {sphinx_path}/{prefix}ix-related-content
        docinfo         = extern
        morphology      = stem_enru
        html_strip      = 1
        charset_type    = utf-8
}

indexer
{
        mem_limit       = 32M
}

searchd
{
        listen                  = {server}:{port}
        log                     = /var/log/sphinx/searchd.log
        query_log               = /var/log/sphinx/query.log
        read_timeout            = 5
        max_children            = 30
        pid_file                = /var/run/sphinx/searchd.pid
        max_matches             = 1000
        seamless_rotate         = 1
        preopen_indexes         = 1
        unlink_old              = 1
        workers                 = threads
        binlog_path             = /var/lib/sphinx
}

EOF;
