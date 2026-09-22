<?php

/**
 * Router.php
 * Router แบบง่าย รองรับ path parameter เช่น /products/{id}
 */

class Router
{
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    public function put(string $pattern, callable $handler): void
    {
        $this->add('PUT', $pattern, $handler);
    }

    public function delete(string $pattern, callable $handler): void
    {
        $this->add('DELETE', $pattern, $handler);
    }

    private function add(string $method, string $pattern, callable $handler): void
    {
        $this->routes[] = [
            'method'  => $method,
            'pattern' => trim($pattern, '/'),
            'handler' => $handler
        ];
    }

    /**
     * จับคู่ method + uri กับ route
     */
    public function dispatch(string $method, string $uri): void
    {
        $uri = trim($uri, '/');

        foreach ($this->routes as $route) {

            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->matchPattern($route['pattern'], $uri);

            if ($params !== false) {

                // ==============================
                // POST / PUT → อ่าน JSON Body
                // ==============================
                if ($method === 'POST' || $method === 'PUT') {

                    $rawData = file_get_contents('php://input');

                    $data = json_decode($rawData, true);

                    // ถ้า JSON ไม่ถูกต้อง
                    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                        Response::error("JSON ไม่ถูกต้อง");
                        return;
                    }

                    // ส่ง path parameter + data
                    $params[] = $data ?? [];
                }

                // เรียก Controller
                call_user_func_array($route['handler'], $params);

                return;
            }
        }

        Response::notFound("ไม่พบ Endpoint: [$method] /$uri");
    }

    /**
     * เทียบ pattern เช่น
     * products/{id}
     * กับ
     * products/102
     */
    private function matchPattern(string $pattern, string $uri)
    {
        $patternSegments = $pattern === ''
            ? []
            : explode('/', $pattern);

        $uriSegments = $uri === ''
            ? []
            : explode('/', $uri);

        if (count($patternSegments) !== count($uriSegments)) {
            return false;
        }

        $params = [];

        foreach ($patternSegments as $i => $segment) {

            if (preg_match('/^\{(\w+)\}$/', $segment, $matches)) {

                // เช่น {id} → เก็บค่า 102
                $params[] = $uriSegments[$i];

            } elseif ($segment !== $uriSegments[$i]) {

                return false;
            }
        }

        return $params;
    }
}

?>