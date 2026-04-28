<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Ciudades fijas (mismo criterio que el catálogo usado en clientes / asociados en el panel consultor), ampliado.
 *
 * @return list<string>
 */
final class CiudadesColombia
{
    /**
     * @return list<string>
     */
    public static function opciones(): array
    {
        $c = [
            'Armenia', 'Barranquilla', 'Bogotá', 'Bucaramanga', 'Cali', 'Cartagena', 'Cúcuta', 'Ibagué', 'Manizales',
            'Medellín', 'Montería', 'Palmira', 'Pasto', 'Pereira', 'Popayán', 'Santa Marta', 'Sincelejo', 'Tuluá', 'Tumaco',
            'Valledupar', 'Villavicencio',
        ];
        $c = array_values(array_unique($c));
        sort($c, SORT_STRING | SORT_FLAG_CASE);

        return $c;
    }
}
