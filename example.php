<?php

use App\ImageHash\Hash;
use App\ImageHash\ImageHash;

require_once 'main.php';

$imagePath = 'example.jpeg';
$hashes = [];
foreach (ImageHash::cases() as $case) {
    $hashes[$case->name] = Hash::shared()->getHash($case, $imagePath);
}

var_dump($hashes);