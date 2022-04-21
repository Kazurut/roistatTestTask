<?php

function openFile(string $fileName, string $mode, bool $useIncludePath = false)
{
    return fopen($fileName, $mode, $useIncludePath);
}

function arrayKeyExists($key, $array): bool
{
    return array_key_exists($key, $array);
}

function fileExists(string $fileName): bool
{
    return file_exists($fileName);
}