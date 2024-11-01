<?php
?>
<div class="wrap">
    <h2><?php echo get_admin_page_title() ?></h2>

    <hr/>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="postbox">
                    <div class="inside">
                        <?php if (isset($_POST['isUbError']) && $_POST['isUbError']) :?>
                            <div class="error notice">
                                <p><?php _e('Введены некорректные данные!', 'ub'); ?></p>
                            </div>
                        <?php endif; ?>
                        <form name="dofollow" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ) ?>" method="post">

                            <?php wp_nonce_field( 'unibase','_ubNonce' ); ?>

                            <h3 class="ub-labels">
                                <?php _e('Ваш трекинговый код UniBase', 'ub'); ?>
                            </h3>
                            <label for="ubUserId" value="123">
                                <?php _e('Идентификатор источника', 'ub'); ?>
                            </label>
                            <input
                                    type="text"
                                    placeholder="Например: UB-000435465"
                                    style="width:98%;"
                                    id="ubUserId"
                                    name="ubUserId"
                                    value="<?php echo esc_textarea(get_option('ub_tracking_user_id')); ?>"
                            />
                            <label for="ubSourceId">
                                <?php _e('Цифровой код источника', 'ub'); ?>
                            </label>
                            <input
                                    type="text"
                                    placeholder="Например: 21"
                                    style="width:98%;"
                                    id="ubSourceId"
                                    name="ubSourceId"
                                    value="<?php echo esc_textarea(get_option('ub_tracking_source_id')); ?>"
                            />
                            <p>
                                <?php _e('Трекиновый код UniBase будет вставлен в <code>&lt;head&gt;</code> вашего сайта.', 'ub'); ?>
                            </p>
                            <p class="submit">
                                <input
                                        class="button button-primary"
                                        type="submit"
                                        name="ubSubmit"
                                        value="<?php _e( 'Сохранить', 'ub'); ?>"
                                />
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
