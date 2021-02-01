<?php
defined('ABSPATH') or die;
/**
 * @var $tabs array
 * @var $active_tab string
 * @var $extra array
 */
?>
<div class="optinly-section">
    <div class="optinly-container">
        <h2><?php _e("Optinly", OPTINLY_TEXT_DOMAIN); ?></h2>
        <nav class="nav-tab-wrapper woo-nav-tab-wrapper">
            <?php
            if (!empty($tabs)) {
                $default_tab_attrs = array('src' => '', 'title' => '', 'id' => '');
                foreach ($tabs as $tab) {
                    $tab_attrs = wp_parse_args($tab, $default_tab_attrs);
                    ?>
                    <a href="<?php echo $tab_attrs['src'] ?>"
                       class="nav-tab <?php echo ($tab_attrs['id'] === $active_tab) ? "nav-tab-active" : ""; ?>"><?php echo $tab_attrs['title'] ?></a>
                    <?php
                }
            }
            ?>
        </nav>
        <section>
            <?php
            do_action("optinly_admin_tab_content_{$active_tab}", $extra);
            ?>
        </section>
    </div>
</div>