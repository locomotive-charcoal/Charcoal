<?php

namespace Charcoal\Admin\Property\Input;

use \Charcoal\Admin\Property\AbstractPropertyInput;

/**
 * Audio Property Input
 */
class AudioInput extends AbstractPropertyInput
{

    /**
     * @var boolean $textEnabled
     */
    private $textEnabled = true;

    /**
     * @var boolean $recordingEnabled
     */
    private $recordingEnabled = true;

    /**
     * @var boolean $fileEnabled
     */
    private $fileEnabled = true;

    /**
     * @var mixed $message
     */
    private $message;

    /**
     * @var mixed $audio_data
     */
    private $audio_data;

    /**
     * @var mixed $audio_file
     */
    private $audio_file;

    public function displayAudioWidget()
    {
        return $this->textEnabled() || $this->recordingEnabled() || $this->fileEnabled();
    }

    /** Setters */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
    public function setTextEnabled($textEnabled)
    {
        $this->textEnabled = $textEnabled;
        return $this;
    }
    public function setRecordingEnabled($recordingEnabled)
    {
        $this->recordingEnabled = $recordingEnabled;
        return $this;
    }
    public function setFileEnabled($fileEnabled)
    {
        $this->fileEnabled = $fileEnabled;
        return $this;
    }

    /** Getters */
    public function message() { return $this->message; }
    public function textEnabled() { return $this->textEnabled; }
    public function recordingEnabled() { return $this->recordingEnabled; }
    public function fileEnabled() { return $this->fileEnabled; }
}
