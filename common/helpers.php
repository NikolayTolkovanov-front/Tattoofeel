<?php
/**
 * Yii2 Shortcuts
 * @author Eugene Terentev <eugene@terentev.net>
 * -----
 * This file is just an example and a place where you can add your own shortcuts,
 * it doesn't pretend to be a full list of available possibilities
 * -----
 */

/**
 * @return int|string
 */
function getMyId()
{
    return Yii::$app->user->getId();
}

/**
 * @param string $view
 * @param array $params
 * @return string
 */
function render($view, $params = [])
{
    return Yii::$app->controller->render($view, $params);
}

/**
 * @param $url
 * @param int $statusCode
 * @return \yii\web\Response
 */
function redirect($url, $statusCode = 302)
{
    return Yii::$app->controller->redirect($url, $statusCode);
}

/**
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function env($key, $default = null)
{

    $value = getenv($key) ?? $_ENV[$key] ?? $_SERVER[$key];

    if ($value === false) {
        return $default;
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;

        case 'false':
        case '(false)':
            return false;
    }

    return $value;
}

/**
 * @param string $category the message category.
 * @param string $message the message to be translated.
 * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
 * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
 * [[\yii\base\Application::language|application language]] will be used.
 * @return string the translated message.
 */
function t($category, $message, $params = [], $language = null) {
    return Yii::t($category, $message, $params, $language);
}

/**
 * @param string $message the message to be translated.
 * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
 * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
 * [[\yii\base\Application::language|application language]] will be used.
 * @return string the translated message.
 */
function t_f($message, $params = [], $language = null) {
    return Yii::t('frontend', $message, $params, $language);
}

/**
 * @param string $message the message to be translated.
 * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
 * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
 * [[\yii\base\Application::language|application language]] will be used.
 * @return string the translated message.
 */
function t_b($message, $params = [], $language = null) {
    return Yii::t('backend', $message, $params, $language);
}

/**
 * @param string $message the message to be translated.
 * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
 * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
 * [[\yii\base\Application::language|application language]] will be used.
 * @return string the translated message.
 */
function t_c($message, $params = [], $language = null) {
    return Yii::t('common', $message, $params, $language);
}
