<?php

namespace FreeBuu\ForwardAuth\Entity;

final class PropertyMapper
{
    public static ?\Closure $customMapper;

    public function __invoke(array $data): array
    {
        if (self::$customMapper) {
            return call_user_func(self::$customMapper, $data);
        }
        $attributes = [];
        foreach ($data as $key => $value) {
            $attributes[$key] = (string) $value;
            if (in_array($key, ['groups', 'entitlements'])) {
                $attributes[$key] = array_unique(array_filter(explode('|', $value)));
            }
        }

        return $attributes;
    }
}
