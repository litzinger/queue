<?php

/**
 * @return string|bool
 */
function get_next_tag()
{
    $next_tag = false;
    $last_tag = git_get_last_tag();

    if (parse_tag($last_tag, $parts)) {
        $next_tag = sprintf('%d.%d.%d', $parts['major'], $parts['minor'], (int) $parts['patch'] + 1);
    }

    return $next_tag;
}

/**
 * @param string $tag
 * @param array  $parts
 * @return bool
 */
function is_valid_tag($tag, array &$parts = null)
{
    $valid = parse_tag($tag, $parts);
    $last_tag = git_get_last_tag();

    return $valid && ($last_tag === false || version_compare($tag, $last_tag, '>'));
}

/**
 * @param string $tag
 * @param array  $parts
 * @return bool
 */
function parse_tag($tag, array &$parts = null)
{
    return (bool) preg_match('~(?P<major>\d+)\.(?P<minor>\d+)\.(?P<patch>\d+)(?:-(?P<release>\w+))?~', $tag, $parts);
}
