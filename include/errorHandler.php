<?php
/**
     * Buffer Callback function
     *
     * This function parse output buffer and cuts all system error and warning messages.
     * Function works relatively fast (parsing and replasing time is above 0.003 - 0.005 sec).
     * In function body there are 2 parts for handle system errors and system warnings
     *
     * @param   string $buffer Contents of the buffer.
     * @return  string
     * @access  public
     */
    function fatalErrorHandler($buffer)
    {

        $fatalError = false;
        $parseError = false;
        $warnings = array();

        $pattern = '/<br \/>(\n|\r\n)(<b>(Fatal error|Warning|Parse error|Notice)<\/b>:(.*))<br \/>/';

        if (preg_match_all($pattern.'m', $buffer, $matchesArray, PREG_PATTERN_ORDER))
        {
            foreach ($matchesArray[3] as $eventNum => $eventType)
            {
                if ($eventType == 'Fatal error')
                {
                    $fatalError = $matchesArray[2][$eventNum];
                }
                elseif ($eventType == 'Parse error')
                {
                    $parseError = $matchesArray[2][$eventNum];
                }
                elseif ($eventType == 'Warning' || $eventType == 'Notice')
                {
                    $warnings[] = $matchesArray[2][$eventNum];
                }
            }
            
            $buffer = preg_replace($pattern, '', $buffer);

            if ($fatalError || $parseError)
            {
                // --- Fatal Error handling part --- //
                $buffer .='<h2>Ошибка!</h2><p><font color="red">При обработке вашего запроса произошла непредвиденная ошибка. Пожалуйста, попробуйте позднее.</font></p>';
            }
            elseif ($warnings)
            {
                // --- Warnings handling part --- //
                //$buffer .= implode("<br>",$warnings);
            }
        }

        return $buffer;
    }
    
    ob_start("fatalErrorHandler");
