<?php

namespace Potsky\LaravelLocalizationHelpers\Factory;

use Config;
use Illuminate\Support\Arr;
use PhpCsFixer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class Localization
{
    const NO_LANG_FOLDER_FOUND_IN_THESE_PATHS = 2;
    const NO_LANG_FOLDER_FOUND_IN_YOUR_CUSTOM_PATH = 3;
    const BACKUP_DATE_FORMAT = 'Ymd_His';
    const PREFIX_LARAVEL_CONFIG = 'laravel-localization-helpers.';
    const JSON_HEADER = '//JSON//';

    private static $PHP_CS_FIXER_LEVELS = ['@PhpCsFixer', '@PhpCsFixer:risky', '@PSR1', '@PSR2', '@Symfony', '@Symfony:risky'];
    private static $PHP_CS_FIXER_FIXERS = [
       'align_multiline_comment', 'allow_single_line_closure', 'array_indentation', 'array_syntax', 'before_array_assignments_colon', 'binary_operator_spaces', 'blank_line_after_namespace', 'blank_line_after_opening_tag', 'blank_line_before_statement', 'braces', 'cast_spaces', 'class_attributes_separation', 'class_definition', 'combine_consecutive_issets', 'combine_consecutive_unsets', 'combine_nested_dirname', 'comment_types', 'compact_nullable_typehint', 'concat_space', 'constant_case', 'declare_equal_normalize', 'declare_strict_types', 'dir_constant', 'doctrine_annotation_array_assignment', 'doctrine_annotation_braces', 'doctrine_annotation_indentation', 'doctrine_annotation_spaces', 'elseif', 'encoding', 'ereg_to_preg', 'error_suppression', 'escape_implicit_backslashes', 'explicit_indirect_variable', 'explicit_string_variable', 'final_internal_class', 'fix_built_in', 'fopen_flag_order', 'fopen_flags', 'full_opening_tag', 'fully_qualified_strict_types', 'function_declaration', 'function_to_constant', 'function_typehint_space', 'heredoc_indentation', 'heredoc_to_nowdoc', 'implode_call', 'include', 'increment_style', 'indentation_type', 'is_null', 'line_ending', 'logical_operators', 'lowercase_cast', 'lowercase_keywords', 'lowercase_static_reference', 'magic_constant_casing', 'magic_method_casing', 'method_argument_space', 'method_chaining_indentation', 'modernize_types_casting', 'mt_rand', 'multiline_comment_opening_closing', 'multiline_whitespace_before_semicolons', 'native_constant_invocation', 'native_function_casing', 'native_function_invocation', 'native_function_type_declaration_casing', 'new_with_braces', 'no_alias_functions', 'no_alternative_syntax', 'no_binary_string', 'no_blank_lines_after_class_opening', 'no_break_comment', 'no_closing_tag', 'no_empty_comment', 'no_empty_statement', 'no_extra_blank_lines', 'no_homoglyph_names', 'no_leading_import_slash', 'no_leading_namespace_whitespace', 'no_mixed_echo_print', 'no_multiline_whitespace_around_double_arrow', 'no_null_property_initialization', 'no_short_bool_cast', 'no_short_echo_tag', 'no_singleline_whitespace_before_semicolons', 'no_spaces_after_function_name', 'no_spaces_around_offset', 'no_spaces_inside_parenthesis', 'no_superfluous_elseif', 'no_trailing_comma_in_list_call', 'no_trailing_comma_in_singleline_array', 'no_trailing_whitespace_in_comment', 'no_trailing_whitespace', 'no_unneeded_control_parentheses', 'no_unneeded_curly_braces', 'no_unneeded_final_method', 'no_unreachable_default_argument_value', 'no_unset_cast', 'no_unset_on_property', 'no_unused_imports', 'no_useless_else', 'no_useless_return', 'no_whitespace_before_comma_in_array', 'no_whitespace_in_blank_line', 'non_printable_character', 'normalize_index_brace', 'null_adjustment', 'object_operator_without_whitespace', 'operator', 'ordered_class_elements', 'ordered_imports', 'pow_to_exponentiation', 'protected_to_private', 'psr4', 'rand', 'random_api_migration', 'remove_in_empty_for_expressions', 'return_assignment', 'return_type_declaration', 'scope', 'self_accessor', 'semicolon_after_instruction', 'set_type_to_cast', 'short_scalar_cast', 'simple_to_complex_string_variable', 'single_blank_line_at_eof', 'single_blank_line_before_namespace', 'single_class_element_per_statement', 'single_import_per_statement', 'single_line_after_imports', 'single_line_comment_style', 'single_line_throw', 'single_quote', 'single_trait_insert_per_statement', 'sort_algorithm', 'space_after_semicolon', 'standardize_increment', 'standardize_not_equals', 'statements', 'strict_comparison', 'strict_param', 'strict', 'string_line_ending', 'switch_case_semicolon_to_colon', 'switch_case_space', 'tags', 'ternary_operator_spaces', 'ternary_to_null_coalescing', 'trailing_comma_in_multiline_array', 'trim_array_spaces', 'unary_operator_spaces', 'use_escape_sequences_in_strings', 'visibility_required', 'void_return', 'whitespace_after_comma_in_array', 'yoda_style',
    ];

    /** @var TranslatorInterface $translator */
    protected $translator;

    /** @var MessageBagInterface $messageBag */
    protected $messageBag;

    /**
     * @param \Potsky\LaravelLocalizationHelpers\Factory\MessageBagInterface $messageBag A message bag or a Console
     *                                                                                   object for output reports
     */
    public function __construct(MessageBagInterface $messageBag)
    {
        $this->messageBag = $messageBag;
    }

    /**
     * Get the current used message bag for facades essentially.
     *
     * @return \Potsky\LaravelLocalizationHelpers\Factory\MessageBagInterface
     */
    public function getMessageBag()
    {
        return $this->messageBag;
    }

    /**
     * Get the lang directory path.
     *
     * @param $lang_folder_path
     *
     * @throws \Potsky\LaravelLocalizationHelpers\Factory\Exception
     *
     * @return string the path
     */
    public function getLangPath($lang_folder_path = null)
    {
        if (empty($lang_folder_path)) {
            $paths = [
                base_path().DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'lang',
            ];

            if (function_exists('app_path')) {
                $paths[] = app_path().DIRECTORY_SEPARATOR.'lang';
            }

            foreach ($paths as $path) {
                if (file_exists($path)) {
                    return $path;
                    //@codeCoverageIgnoreStart
                }
            }

            $e = new Exception('', self::NO_LANG_FOLDER_FOUND_IN_THESE_PATHS);
            $e->setParameter($paths);

            throw $e;
        //@codeCoverageIgnoreEnd
        } else {
            if (file_exists($lang_folder_path)) {
                return $lang_folder_path;
            }

            $e = new Exception('', self::NO_LANG_FOLDER_FOUND_IN_YOUR_CUSTOM_PATH);
            $e->setParameter($lang_folder_path);

            throw $e;
        }
    }

    /**
     * Return an absolute path without predefined variables.
     *
     * @param string|array $path the relative path
     *
     * @return array the absolute path
     */
    public function getPath($path)
    {
        if (!is_array($path)) {
            $path = [$path];
        }

        $search_for = [
            '%BASE',
            '%STORAGE',
        ];

        $replace_by = [
            base_path(),
            storage_path(),
        ];

        if (function_exists('app_path')) {
            $search_for[] = '%APP';
            $replace_by[] = app_path();
        }

        if (function_exists('public_path')) {
            $search_for[] = '%PUBLIC';
            $replace_by[] = public_path();
        }

        $folders = str_replace($search_for, $replace_by, $path);

        foreach ($folders as $k => $v) {
            $folders[$k] = realpath($v);
        }

        return $folders;
    }

    /**
     * Return an relative path to the laravel directory.
     *
     * @param string $path the absolute path
     *
     * @return string the relative path
     */
    public function getShortPath($path)
    {
        return str_replace(base_path(), '', $path);
    }

    /**
     * Return an iterator of files with specific extension in the provided paths and subpaths.
     *
     * @param string $path a source path
     * @param string $ext
     *
     * @return array a list of file paths
     */
    public function getFilesWithExtension($path, $ext = 'php')
    {
        if (is_dir($path)) {
            return new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST,
                    \RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
                ),
                '/^.+\.'.$ext.'$/i',
                \RecursiveRegexIterator::GET_MATCH
            );
        } else {
            return [];
        }
    }

    /**
     * Extract all translations from the provided file.
     *
     * Remove all translations containing :
     * - $  -> auto-generated translation cannot be supported
     * - :: -> package translations are not taken in account
     *
     * @param string $path          the file path
     * @param array  $trans_methods an array of regex to catch
     *
     * @return array an array dot of found translations
     */
    public function extractTranslationFromPhpFile($path, $trans_methods)
    {
        $result = [];
        $string = Tools::minifyString(file_get_contents($path));

        foreach (Arr::flatten($trans_methods) as $method) {
            preg_match_all($method, $string, $matches);

            foreach ($matches[1] as $k => $v) {
                if (strpos($v, '$') !== false) {
                    unset($matches[1][$k]);
                }
                if (strpos($v, '::') !== false) {
                    unset($matches[1][$k]);
                }
            }
            $result = array_merge($result, array_flip($matches[1]));
        }

        return $result;
    }

    /**
     * Extract all translations from the provided folders.
     *
     * @param array  $folders            a list of folder to search in
     * @param array  $trans_methods      an array of regex to catch
     * @param string $php_file_extension default is php
     *
     * @return array
     */
    public function extractTranslationsFromFolders($folders, $trans_methods, $php_file_extension = 'php')
    {
        $lemmas = [];

        foreach ($folders as $path) {
            foreach ($this->getFilesWithExtension($path, $php_file_extension) as $php_file_path => $dumb) {
                $lemma = [];

                foreach ($this->extractTranslationFromPhpFile($php_file_path, $trans_methods) as $k => $v) {
                    $a = $this->evalString($k);
                    if (is_string($a)) {
                        $real_value = $a;
                        $lemma[$real_value] = $php_file_path;
                    } else {
                        $this->messageBag->writeError("Unable to understand string $k");
                    }
                }

                $lemmas = array_merge($lemmas, $lemma);
            }
        }

        return $lemmas;
    }

    /**
     * @param array  $lemmas                   an array of lemma
     *                                         eg: [ 'message.lemma.child' => string(83)
     *                                         "/Users/potsky/WTF/laravel-localization-helpers/tests/mock/trans.php" , ... ]
     * @param string $dot_notation_split_regex
     * @param int    $level
     *
     * @return array a structured array of lemma
     *               eg: array(1) {
     *               'message' =>
     *               array(2) {
     *               'lemma' =>
     *               array(9) {
     *               'child' => string(83)
     *               "/Users/potsky/Work/Private/GitHub/laravel-localization-helpers/tests/mock/trans.php"
     *               ...
     */
    public function convertLemmaToStructuredArray($lemmas, $dot_notation_split_regex, $level = -1)
    {
        $lemmas_structured = [];

        if (!is_string($dot_notation_split_regex)) {
            // fallback to dot if provided regex is not a string
            $dot_notation_split_regex = '/\\./';
        }

        foreach ($lemmas as $key => $value) {
            $keys = preg_split($dot_notation_split_regex, $key, $level);

            if (count($keys) <= 1) {
                Tools::arraySet($lemmas_structured, self::JSON_HEADER.'.'.$key, $value, $dot_notation_split_regex, $level);
            } else {
                Tools::arraySet($lemmas_structured, $key, $value, $dot_notation_split_regex, $level);
            }
        }

        return $lemmas_structured;
    }

    /**
     * @param array $lemmas an array of lemma
     *                      eg: [ 'message.lemma.child' => string(83)
     *                      "/Users/potsky/WTF/laravel-localization-helpers/tests/mock/trans.php" , ... ]
     *
     * @return array a flat array of lemma
     *               eg: array(1) {
     *               'message' =>
     *               array(2) {
     *               'lemma.child' => string(83)
     *               "/Users/potsky/Work/Private/GitHub/laravel-localization-helpers/tests/mock/trans.php"
     *               ...
     */
    public function convertLemmaToFlatArray($lemmas)
    {
        return $this->convertLemmaToStructuredArray($lemmas, null, 2);
    }

    /**
     * @param int $offsetDay the count of days to subtract to the current time
     *
     * @return bool|string current date
     */
    public function getBackupDate($offsetDay = 0)
    {
        $now = new \DateTime();
        $now->sub(new \DateInterval('P'.(int) $offsetDay.'D'));

        return $now->format(self::BACKUP_DATE_FORMAT);
    }

    /**
     * Return all lang backup files.
     *
     * @param string $lang_directory the lang directory
     * @param string $ext
     *
     * @return array
     */
    public function getBackupFiles($lang_directory, $ext = 'php')
    {
        $files = $lang_directory.'/*/*[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]_[0-9][0-9][0-9][0-9][0-9][0-9].'.$ext;

        return glob($files);
    }

    /**
     * Delete backup files.
     *
     * @param string     $lang_folder_path
     * @param int        $days
     * @param bool|false $dryRun
     * @param string     $ext
     *
     * @return bool
     */
    public function deleteBackupFiles($lang_folder_path, $days = 0, $dryRun = false, $ext = 'php')
    {
        if ($days < 0) {
            return false;
        }

        try {
            $dir_lang = $this->getLangPath($lang_folder_path);
        } catch (Exception $e) {
            switch ($e->getCode()) {
                //@codeCoverageIgnoreStart
                case self::NO_LANG_FOLDER_FOUND_IN_THESE_PATHS:
                    $this->messageBag->writeError('No lang folder found in these paths:');
                    foreach ($e->getParameter() as $path) {
                        $this->messageBag->writeError('- '.$path);
                    }
                    break;
                //@codeCoverageIgnoreEnd

                case self::NO_LANG_FOLDER_FOUND_IN_YOUR_CUSTOM_PATH:
                    $this->messageBag->writeError('No lang folder found in your custom path: "'.$e->getParameter().'"');
                    break;
            }

            return false;
        }

        $return = true;

        foreach ($this->getBackupFiles($dir_lang) as $file) {
            $fileDate = $this->getBackupFileDate($file, $ext);

            // @codeCoverageIgnoreStart
            // Cannot happen because of glob but safer
            if (is_null($fileDate)) {
                $this->messageBag->writeError('Unable to detect date in file '.$file);
                $return = false;

                continue;
            }
            // @codeCoverageIgnoreEnd

            if ($this->isDateOlderThanDays($fileDate, $days)) {
                if ($dryRun === true) {
                    $deleted = true;
                } else {
                    $deleted = unlink($file);
                }

                if ($deleted === true) {
                    $this->messageBag->writeInfo('Deleting file '.$file);
                }
                // @codeCoverageIgnoreStart
                else {
                    $this->messageBag->writeError('Unable to delete file '.$file);

                    $return = false;
                }
            }
            // @codeCoverageIgnoreEnd
            else {
                $this->messageBag->writeInfo('Skip file '.$file.' (not older than '.$days.'day'.Tools::getPlural($days).')');
            }
        }

        return $return;
    }

    /**
     * @param \DateTime $date
     * @param int       $days
     *
     * @return bool
     */
    public function isDateOlderThanDays(\DateTime $date, $days)
    {
        $now = new \DateTime();

        return  $now->diff($date)->format('%a') >= $days;
    }

    /**
     * Eval a PHP string and catch PHP Parse Error syntax.
     *
     * @param $str
     *
     * @return bool|mixed
     */
    private function evalString($str)
    {
        $a = false;

        if (class_exists('ParseError')) {
            try {
                $a = eval("return $str;");
            } catch (\ParseError $e) {
            }
        } else {
            $a = @eval("return $str;");
        }

        return $a;
    }

    /**
     * Get the list of PHP code files where a lemma is defined.
     *
     * @param string     $lemma         A lemma to search for or a regex to search for
     * @param array      $folders       An array of folder to search for lemma in
     * @param array      $trans_methods An array of PHP lang functions
     * @param bool|false $regex         Is lemma a regex ?
     * @param bool|false $shortOutput   Output style for file paths
     * @param string     $ext
     *
     * @return array|false
     */
    public function findLemma($lemma, $folders, $trans_methods, $regex = false, $shortOutput = false, $ext = 'php')
    {
        $files = [];

        foreach ($folders as $path) {
            foreach ($this->getFilesWithExtension($path, $ext) as $php_file_path => $dumb) {
                foreach ($this->extractTranslationFromPhpFile($php_file_path, $trans_methods) as $k => $v) {
                    $a = $this->evalString($k);
                    if (is_string($a)) {
                        $real_value = $a;
                        $found = false;

                        if ($regex) {
                            try {
                                $r = preg_match($lemma, $real_value);
                            }
                            // Exception is thrown via command
                            catch (\Exception $e) {
                                $this->messageBag->writeError('The argument is not a valid regular expression:'.str_replace('preg_match():', '', $e->getMessage()));

                                return false;
                            }
                            if ($r === 1) {
                                $found = true;
                            }
                            // Normal behavior via method call
                            // @codeCoverageIgnoreStart
                            elseif ($r === false) {
                                $this->messageBag->writeError('The argument is not a valid regular expression');

                                return false;
                            }
                            // @codeCoverageIgnoreEnd
                        } else {
                            if (!(strpos($real_value, $lemma) === false)) {
                                $found = true;
                            }
                        }

                        if ($found === true) {
                            if ($shortOutput === true) {
                                $php_file_path = $this->getShortPath($php_file_path);
                            }
                            $files[] = $php_file_path;
                            break;
                        }
                    } else {
                        $this->messageBag->writeError("Unable to understand string $k");
                    }
                }
            }
        }

        return $files;
    }

    /**
     * @param string $word
     * @param string $to
     * @param null   $from
     *
     * @return mixed
     */
    public function translate($word, $to, $from = null)
    {
        if (is_null($this->translator)) {
            /** @var string $translator */
            $translator = config(self::PREFIX_LARAVEL_CONFIG.'translator');
            $this->translator = new Translator(config(self::PREFIX_LARAVEL_CONFIG.'translator'), [
                'client_id'        => config(self::PREFIX_LARAVEL_CONFIG.'translators.'.$translator.'.client_id'),
                'client_secret'    => config(self::PREFIX_LARAVEL_CONFIG.'translators.'.$translator.'.client_secret'),
                'default_language' => config(self::PREFIX_LARAVEL_CONFIG.'translators.'.$translator.'.default_language'),
            ]);
        }

        $translation = $this->translator->translate($word, $to, $from);

        if (is_null($translation)) {
            $translation = $word;
        }

        return $translation;
    }

    /**
     * Fix Code Style for a file or a directory.
     *
     * @param       $filePath
     * @param array $fixers
     * @param null  $level
     *
     * @throws \Exception
     * @throws \Potsky\LaravelLocalizationHelpers\Factory\Exception
     *
     * @return string
     */
    public function fixCodeStyle($filePath, array $fixers, $level = null)
    {
        if (!file_exists($filePath)) {
            throw new Exception('File "'.$filePath.'" does not exist, cannot fix it');
        }

        $options = [
            'command' => 'fix',
            'path'    => [$filePath],
        ];

        $fix = [];
        foreach ($fixers as $key => $value) {
            if(is_int($key)){
                if ($this->isAFixer($value)) {
                    $fix[$value] = true;
                }
                continue;
            }
            if ($this->isAFixer($key)) {
                $fix[$key] = $value;
            }
        }
        $options['--rules'] = json_encode($fix);

        if ($this->isALevel($level)) {
            $fix[$level] = true;
        }

        $input = new ArrayInput($options);
        $output = new BufferedOutput();
        $application = new Application();
        $application->setAutoExit(false);
        $application->run($input, $output);

        return $output->fetch();
    }

    /**
     * Tell if the provided fixer is a valid fixer.
     *
     * @param string $fixer
     *
     * @return bool
     */
    public function isAFixer($fixer)
    {
        return in_array($fixer, self::$PHP_CS_FIXER_FIXERS);
    }

    /**
     * Tell if the provided level is a valid level.
     *
     * @param string $level
     *
     * @return bool
     */
    public function isALevel($level)
    {
        return in_array($level, self::$PHP_CS_FIXER_LEVELS);
    }

    /**
     * Get the backup file path according to the current file path.
     *
     * @param string $file_lang_path
     * @param string $date
     * @param string $ext
     *
     * @return mixed
     */
    public function getBackupPath($file_lang_path, $date, $ext = 'php')
    {
        return preg_replace('/\.'.$ext.'$/', '.'.$date.'.'.$ext, $file_lang_path);
    }

    /**
     * Return the date of a backup file.
     *
     * @param string $file a backup file path
     * @param string $ext
     *
     * @return \DateTime|null
     */
    private function getBackupFileDate($file, $ext = 'php')
    {
        $matches = [];

        if (preg_match('@^(.*)([0-9]{8}_[0-9]{6})\\.'.$ext.'$@', $file, $matches) === 1) {
            return \DateTime::createFromFormat(self::BACKUP_DATE_FORMAT, $matches[2]);
        }
        // @codeCoverageIgnoreStart
        // Cannot happen because of glob but safer
        else {
            return null;
        }
        // @codeCoverageIgnoreEnd
    }
}
