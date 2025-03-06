<?php

/**
 * @return string
 */
function git_get_branch()
{
    static $branch;

    if ($branch === null) {
        exec('git branch | grep \\* | cut -d \' \' -f2 2>&1', $output, $return);

        if ($return !== 0) {
            return false;
        } else {
            $branch = $output[0];
        }
    }

    return $branch;
}

/**
 * @return string|bool
 */
function git_get_last_tag()
{
    static $last_tag;

    if ($last_tag === null) {
        exec('git describe --tags --abbrev=0 2>&1', $output, $return);

        if ($return !== 0) {
            return false;
        } else {
            $last_tag = $output[0];
        }
    }

    return $last_tag;
}

/**
 * @param string $tag
 * @return bool
 */
function git_create_release($tag)
{
    return git_checkout('main') &&
           git_merge('develop') &&
           git_tag($tag) &&
           git_checkout('develop') &&
           git_merge('main', true);
}

/**
 * @param string $branch
 * @return bool
 */
function git_checkout($branch)
{
    exec(sprintf('git checkout \'%s\' 2>&1', $branch), $output, $return);
    return $return === 0;
}

/**
 * @param string $branch
 * @param bool   $fast_forward
 * @return bool
 */
function git_merge($branch, $fast_forward = false)
{
    $ff = $fast_forward ? '--ff' : '--no-ff';
    exec(sprintf('git merge %s \'%s\' 2>&1', $ff, $branch), $output, $return);
    return $return === 0;
}

/**
 * @param string $tag
 * @return bool
 */
function git_tag($tag)
{
    exec(sprintf('git tag \'%s\' 2>&1', $tag), $output, $return);
    return $return === 0;
}

/**
 * @return bool
 */
function git_get_hash()
{
    exec('git rev-parse HEAD', $output, $return);

    if ($return !== 0) {
        return false;
    }

    return substr($output[0], 0, 8);
}
