<?php

namespace App\Services\Traits;

use App\Models\NewsTranslation;
use App\Models\Translation;
use App\Services\LanguageService;
use Illuminate\Http\Request;

trait LanguageTrait
{

    /**
     * @param object $translations
     * @param $user_lang
     * @return string
     */
    public function getTranslation(object $translations, $user_lang)
    {
        $result = '';
        $default_lang = config('app.default_lang');
        // try set user or default language
        foreach ($translations as $translation) {
            if ($translation->value) {
                if ($translation->lang === $user_lang) {
                    $result = $translation->value;
                    break;
                }
                if ($translation->lang === $default_lang) {
                    $result = $translation->value;
                }
            }
        }
        // if not text on default language - set anyone
        if (!$result) {
            foreach ($translations as $translation) {
                if ($translation->value) {
                    $result = $translation->value;
                    break;
                }
            }
        }

        return $result
            ? $result
            : '';
    }

    /**
     * @param string $field_name
     * @param int $name_code
     * @param Request $request
     * @param $type_code
     * @param bool $use_news_translations_table
     */
    public function create_or_update_translations(
        string $field_name,
        int $name_code,
        Request $request,
        $type_code,
        $use_news_translations_table = false
    )
    {
        foreach (LanguageService::get_languages() as $language) {
            $translations_class = $use_news_translations_table ? NewsTranslation::class : Translation::class;
            $translation = $translations_class::where([
                'code' => $name_code,
                'type' => $type_code,
                'lang' => $language->name,
            ])->first();
            if ($translation) {
                $translation->update([
                    'value' => $request->get(($field_name . '_' . $language->name), '') ?? '',
                ]);
            } else {
                $translations_class::create([
                    'code' => $name_code,
                    'type' => $type_code,
                    'lang' => $language->name,
                    'value' => $request->get(($field_name . '_' . $language->name), '') ?? '',
                ]);
            }
        }
    }

    /**
     * @param object $translations
     * @return array
     */
    public function getTranslations(object $translations)
    {
        $result = [];
        foreach ($translations as $translation) {
            $result[$translation->lang] = $translation->value;
        }

        return $result;
    }
}
