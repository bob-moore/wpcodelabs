<?php

namespace mdm\cornerstone\interfaces;

/**
 * ShortcodeHookSubscriberInterface is used by an object that needs to subscribe to
 * WordPress shortcode hooks.
 */
interface ShortcodeHookSubscriber
{
    /**
     * Returns an array of shortcode that the object needs to be subscribed to.
     *
     * The array key is the name of the shortcode hook. The value can be:
     *
     *  * The method name
     *  * An array with the method name and priority
     *  * An array with the method name, priority and number of accepted arguments
     *
     * For instance:
     *
     *  * array('shortcode_name' => 'method_name')
     *
     * @return array
     */
    public function get_shortcodes();
}