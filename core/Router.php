<?php
namespace Core;

class Router
{
    private static array $getRoutes = [];
    private static array $postRoutes = [];

    private const REGEX_PLACEHOLDERS = [
        'courseId' => '\d+',
        'courseTitle' => '[a-zA-Z\_-]+'
    ];

    // * -----------------------------------------------------------------------------------------------------
    // * ----------------------------DEFINING URL METHODS ----------------------------------------------------
    // ? ---------Adding Regex to Defined URLs to Preg_match later with CURRENT url---------------------------

    private static function convertUrlToRegex(string $urlRoute): string
    {
        $regexPattern = '/\{(\w+)\}/';
        $urlRoute = preg_replace_callback($regexPattern,
            function ($foundPattern) {
                $placeholderMatch = $foundPattern[1];  // 0 = hole match {(group)} | 1 = found group (group only) | 1 MATCH PER ROUND

                foreach (self::REGEX_PLACEHOLDERS as $placeholder => $regex) {
                    if ($placeholder === $placeholderMatch) {
                        return "(?P<$placeholderMatch>$regex)";
                    }
                }
                return "(?P<$placeholderMatch>[a-zA-Z0-9]+)";
            }, $urlRoute);

        $urlRoute = "#^$urlRoute(\?.*)?\$#";  // (\?.*)? => (allow ? and any character)(? optional) for query like this ?page=1&...etc
        // die($urlRoute); //? DEBUG
        return $urlRoute;
    }

    /**
     * @param string $urlRoute The predefined URL routes that are valid
     * @param array $callable The callable method using call_user_func
     */
    public static function get(string $urlRoute, array $callabe): void
    {
        $urlRoute = self::convertUrlToRegex($urlRoute);
        self::$getRoutes[$urlRoute] = $callabe;
    }

    /**
     * @param string $urlRoute The predefined URL routes that are valid
     * @param array $callable The callable method using call_user_func
     */
    public static function post(string $urlRoute, array $callabe): void
    {
        $urlRoute = self::convertUrlToRegex($urlRoute);
        self::$postRoutes[$urlRoute] = $callabe;
    }

    // * -----------------------------------------------------------------------------------------------------
    // * ----------------------------CURRENT URL METHODS -----------------------------------------------------
    // ? -------------------Comparing Current url with defined urls-------------------------------------------

    /**
     * @param string $currentUrl The current url the user is visiting
     * @param string $requestMethod The request method of the server. GET OR POST
     */
    public static function dispatch(string $currentUrl, string $requesMethod): void
    {
        // die($currentUrl);  // ? DEBUG

        if ($requesMethod === 'GET') {
            self::handle($currentUrl, self::$getRoutes);
        } elseif ($requesMethod === 'POST') {
            self::handle($currentUrl, self::$postRoutes);
        }
    }

    private static function handle(string $currentUrl, array $controller): void
    {
        // die(var_dump($controller));
        foreach ($controller as $regexPattern => $callabe) {
            if (preg_match($regexPattern, $currentUrl, $matches)) {
                $matches = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                // die(var_dump($matches));
                if ($matches) {
                    call_user_func_array($callabe, $matches);
                } else {
                    call_user_func($callabe);
                }
                return;
            }
        }

        echo 'Error: Handle';
        self::redirect('404');
    }

    // * ----------------------------------------------------------------------------------------------------
    // * ----------------------------CURRENT URL REDIRECT ---------------------------------------------------
    // ? ------------------------------REDIRECTING URLS------------------------------------------------------

    /**
     * @param string $location Where the url should redirect to
     */
    public static function redirect(string $location)
    {
        header('location: ' . trim($location, '/'));
        exit();
    }
}
