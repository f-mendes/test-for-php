<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\CacheInterface;
use DateTimeZone;
use DateTime;
use DateInterval;

use function sha1;
use function str_replace;


class MemoryCache implements CacheInterface
{
    private array $cache = [];

    public function pull(string $key): array
    {   
        $key = $this->setkey($key);

        if(isset($this->cache[$key])){

            /**
             * Essa condição verifica se já expirou o tempo de vida dos dados
             * Se verdadeiro remove do cache
             */
            if($this->cache[$key]['ttl'] < $this->getDateCurrent()){
                $this->remove($key);
                return [];
            }

            return $this->cache[$key]['value'];
        }

        return [];
    }
 
    public function set(string $key, array $value, int $ttl): bool
    {
        $key = $this->setkey($key);
        $time = $this->setTimeToLive($ttl);

        $this->cache[$key] = ['value' => $value, 'ttl' => $time];

        return true;
    }

    public function remove(string $key): bool
    {   
        $key = $this->setkey($key);
        unset($this->cache[$key]);
        return true;
    }

    public function clear(): bool
    {   
        $this->cache = [];
        return true;
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

    /**
    * Desenvolvi esse método para definir um padrão de chave única 
    * baseado no endpoint da requisição
    */
    private function setkey(string $key): string
    {
        return sha1(str_replace(['/','?','=','&'],'_',$key));
    }

}