<?php

namespace Wordless\Abstractions\StubMounters;

use Wordless\Exception\FailedToCopyStub;
use Wordless\Exception\PathNotFoundException;
use Wordless\Helpers\ProjectPath;

abstract class BaseStubMounter
{
    protected array $replace_content_dictionary;
    private string $new_file_path;
    private string $stub_filepath;
    private string $stub_unfilled_content;

    abstract protected function relativeStubFilename(): string;

    /**
     * @param string $new_file_path
     * @throws PathNotFoundException
     */
    public function __construct(string $new_file_path)
    {
        $this->stub_unfilled_content = file_get_contents(
            $this->stub_filepath = ProjectPath::stubs($this->relativeStubFilename())
        );
        $this->new_file_path = $new_file_path;
    }

    /**
     * @throws FailedToCopyStub
     */
    public function mountNewFile(?string $new_file_path = null)
    {
        $new_file_path = $new_file_path ?? $this->new_file_path;

        if (file_put_contents($new_file_path, $this->replaceUnfilledContent()) === false) {
            throw new FailedToCopyStub($this->stub_filepath, $new_file_path);
        }
    }

    private function replaceUnfilledContent(): string
    {
        if (empty($this->replace_content_dictionary)) {
            return $this->stub_unfilled_content;
        }

        $search = [];
        $replace = [];

        foreach ($this->replace_content_dictionary as $original_content => $new_content_value) {
            $search[] = $original_content;
            $replace[] = $new_content_value;
        }

        return str_replace($search, $replace, $this->stub_unfilled_content);
    }

    /**
     * @param array $replace_content_dictionary
     * @return $this
     */
    public function setReplaceContentDictionary(array $replace_content_dictionary): BaseStubMounter
    {
        $this->replace_content_dictionary = $replace_content_dictionary;

        return $this;
    }
}