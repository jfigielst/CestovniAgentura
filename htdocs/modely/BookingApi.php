<?php

class BookingApi
{
    private $apiKey = '11bc590b90msh410710abd90409bp13ad44jsnfccf414ef272';
    private $apiHost = 'booking-com15.p.rapidapi.com';
    private $cacheDir = __DIR__ . '/../cache/';
    
    /**
     * Provede HTTP GET dotaz na RapidAPI s podporou Cache.
     */
    private function fetchWithCache($endpoint, $queryParams = [], $cacheMinuty = 60)
    {
        $url = "https://" . $this->apiHost . $endpoint;
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        // Vytvoříme bezpečný název souboru pro cache na základě URL
        $cacheFile = $this->cacheDir . md5($url) . '.json';
        
        // Zkusíme najít platnou cache
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheMinuty * 60)) {
            $cachedData = file_get_contents($cacheFile);
            return json_decode($cachedData, true);
        }

        // Nastavení cURL
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: " . $this->apiHost,
                "x-rapidapi-key: " . $this->apiKey
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return ["error" => "cURL Error #:" . $err];
        } else {
            $decoded = json_decode($response, true);
            
            // Uložíme do cache jen pokud odpověď neobsahuje chybu (např. 403 Forbidden zprávy)
            if ($decoded && !isset($decoded['message'])) {
                file_put_contents($cacheFile, $response);
            }
            return $decoded;
        }
    }

    /**
     * 1. Krok: Najde ID destinace podle názvu města
     */
    public function najdiDestinaci($query)
    {
        // Příklad endpointu (může se lišit dle konkrétního API na RapidAPI, často to bývá /api/v1/hotels/searchDestination)
        $endpoint = "/api/v1/hotels/searchDestination";
        $data = $this->fetchWithCache($endpoint, ['query' => $query], 1440); // 24h cache na města
        
        // Pokud API vrací chybu (např. 403)
        if (isset($data['message'])) {
            return ['error' => $data['message']];
        }

        // Extrahuje první nalezené ID (struktura JSON se liší dle verze API, běžně to je data[0]->dest_id)
        if (isset($data['data']) && is_array($data['data']) && count($data['data']) > 0) {
            return [
                'dest_id' => $data['data'][0]['dest_id'],
                'dest_type' => $data['data'][0]['dest_type']
            ];
        }
        
        return null;
    }

    /**
     * 2. Krok: Hledání hotelů
     */
    public function najdiHotely($destId, $destType, $prijezd, $odjezd, $dospeli)
    {
        $endpoint = "/api/v1/hotels/searchHotels";
        $params = [
            'dest_id' => $destId,
            'search_type' => $destType,
            'arrival_date' => $prijezd,
            'departure_date' => $odjezd,
            'adults' => $dospeli,
            'children_age' => '0,17',
            'room_qty' => 1,
            'page_number' => 1,
            'units' => 'metric',
            'temperature_unit' => 'c',
            'languagecode' => 'cs-cz',
            'currency_code' => 'CZK'
        ];
        
        $data = $this->fetchWithCache($endpoint, $params, 60); // 1h cache na ceny
        
        return $data;
    }
}
