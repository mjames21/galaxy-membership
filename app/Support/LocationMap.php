<?php

namespace App\Support;

final class LocationMap
{
    private array $map;

    private function __construct(array $map) { $this->map = $map; }

    public static function for(string $country = 'sierra_leone'): self
    {
        return new self(config('locations.'.$country, []));
    }

    public function regions(): array
    {
        $out = [];
        foreach ($this->map as $id => $p) $out[] = ['id' => (int)$id, 'name' => $p['name']];
        return $out;
    }

    public function districts(?int $r): array
    {
        if (!$r || !isset($this->map[$r])) return [];
        $out = [];
        foreach ($this->map[$r]['districts'] as $id => $d) $out[] = ['id' => (int)$id, 'name' => $d['name']];
        return $out;
    }

    public function constituencies(?int $r, ?int $d): array
    {
        if (!$r || !$d) return [];
        $dist = $this->map[$r]['districts'][$d] ?? null; if (!$dist) return [];
        $out = [];
        foreach ($dist['constituencies'] as $id => $c) $out[] = ['id' => (int)$id, 'name' => $c['name']];
        return $out;
    }

    public function zones(?int $r, ?int $d, ?int $c): array
    {
        if (!$r || !$d || !$c) return [];
        $cons = $this->map[$r]['districts'][$d]['constituencies'][$c] ?? null; if (!$cons) return [];
        $out = [];
        foreach ($cons['zones'] as $id => $name) $out[] = ['id' => (int)$id, 'name' => $name];
        return $out;
    }

    public function validRegion(?int $id): bool
    { return $id ? isset($this->map[$id]) : true; }

    public function validDistrict(?int $r, ?int $d): bool
    { return (!$r || !$d) ? !$d : isset($this->map[$r]['districts'][$d]); }

    public function validConstituency(?int $r, ?int $d, ?int $c): bool
    { return (!$r || !$d || !$c) ? !$c : isset($this->map[$r]['districts'][$d]['constituencies'][$c]); }

    public function validZone(?int $r, ?int $d, ?int $c, ?int $z): bool
    { return (!$r || !$d || !$c || !$z) ? !$z : isset($this->map[$r]['districts'][$d]['constituencies'][$c]['zones'][$z]); }
}
