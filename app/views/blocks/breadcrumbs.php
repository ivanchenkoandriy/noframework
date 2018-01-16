<?php
/**
 * Template to display block for bread crumbs
 *
 * @var array $links Links of bread crumbs
 */
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <ol class="breadcrumb">
                <?php
                $active = array_pop($links);

                reset($links);
                while ($link = current($links)) {
                    echo '<li><a href="' . $link['url'] . '">' . $link['title'] . '</a></li>';
                    next($links);
                }
                if ($active) {
                    echo '<li class="active">' . $active['title'] . '</li>';
                }
                ?>
            </ol>
        </div>
    </div>
</div>