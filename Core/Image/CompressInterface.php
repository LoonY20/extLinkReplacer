<?php


namespace Image;


interface CompressInterface
{

    public function resize(): void;
    public function compress(string $image): bool;

}