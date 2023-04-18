<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\CacheInterface;
use DateTime;
use DateTimeZone;
use DateInterval;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

use function dirname;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function mkdir;
use function rmdir;
use function serialize;
use function sha1;
use function substr;
use function unlink;
use function unserialize;



class FileCache implements CacheInterface
{
    protected string $dir = '../storage/cache/';

    
    public function pull(string $key): array
    {
        $file = $this->getFilePath($key);

        if(!file_exists($file)){
            return [];
        }

        $content = file_get_contents($file);
        if ($content === false) {
            return [];
        }

        $data = unserialize($content);
        if(!is_array($data)){
            return [];
        }

        /**
         * Essa condição verifica se já expirou o tempo de vida dos dados
         * Se verdadeiro remove do cache
         */
        if($data['ttl'] < $this->getDateCurrent()){
            $this->remove($key);
            return [];
        }

        return $data['value'];
    }

    public function set(string $key, array $value, int $ttl): bool
    {
        $file = $this->getFilePath($key);
        $time = $this->setTimeToLive($ttl);

        $dir = dirname($file);
        if(!file_exists($dir)){
            mkdir($dir, 0777, true);
        }

        $data = serialize(['value' => $value, 'ttl' => $time]);

        return file_put_contents($file, $data) !== false;
    }

    public function remove(string $key): bool
    {
        $file = $this->getFilePath($key);
        return file_exists($file) ? unlink($file) : true;
    }

    /** 
    *  Nesse método eu usei a classe RecursiveIteratorIterator 
    *  para criar um iterador recursivo para diretórios
    */
    public function clear(): bool
    {       
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDir()) {
                rmdir($fileinfo->getRealPath());
            } else {
                if($fileinfo->getFilename() !== '.gitkeep')
                    unlink($fileinfo->getRealPath());
            }
        }
        return true;

    }

     /**
     * Essa função cria a estrura de diretórios e o arquivo de cache baseado no endpoint da requisição
     * Eu segui o padrão de diretório de cache do Laravel
     */
    private function getFilePath(string $key): string
    {       
        $hash = sha1(str_replace(['/','?','=','&'],'_',$key));
        return $this->dir . substr($hash, 0, 2) . '/' . substr($hash, 2, 2) . '/' . $hash;
    }

    /**
     * Desenvolvi esse método para definir a data de expiração do cache
     */
    private function setTimeToLive(int $ttl): object
    {   
        $tz = new DateTimeZone("America/Sao_Paulo");

        $dateInterval = new DateInterval("PT{$ttl}S");
        $date = new DateTime('now',$tz);

        return $date->add($dateInterval);

    }

    private function getDateCurrent(): object
    {   
        $tz = new DateTimeZone("America/Sao_Paulo");
        return new DateTime('now',$tz);
    }
}