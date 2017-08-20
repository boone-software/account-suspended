<!DOCTYPE html>
<html>
<head>
    <meta charset="<?php echo esc_attr($charset); ?>">
    <title><?php echo stripslashes($title); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="author" content="<?php echo esc_attr($author); ?>" />
    <meta name="description" content="<?php echo esc_attr($description); ?>" />
    <meta name="keywords" content="<?php echo esc_attr($keywords); ?>" />
    <meta name="robots" content="<?php echo esc_attr($robots); ?>" />
<?php
    if (!empty($styles) && is_array($styles)) {
        foreach ($styles as $src) {
?>
    <link rel="stylesheet" href="<?php echo $src; ?>">
<?php
        }
    }

        // do some actions
        do_action('bsas_head');
?>
</head>
<body<?php echo (!empty($body_classes)) ? sprintf(' class="%s"', $body_classes) : ''; ?>>
<?php do_action('bsas_after_body'); ?>
    <section class="wrapper">
        <header>
            <h1>Account Suspended</h1>
        </header>

        <main>
            <p>
                We apologize for the inconvenience, but this account has been
                suspended until further notice. It will be restored in due time.
            </p>
        </main>
    </section>

    <script>
        var bsas_vars = {
            'ajax_url': '<?php echo admin_url('admin-ajax.php'); ?>',
        };
    </script>
<?php
    if (!empty($scripts) && is_array($scripts)) {
        foreach ($scripts as $src) {
?>
    <script src="<?php echo $src; ?>"></script>
<?php
        }
    }

    // Do some actions
    do_action('bsas_footer');
?>
</body>
</html>
