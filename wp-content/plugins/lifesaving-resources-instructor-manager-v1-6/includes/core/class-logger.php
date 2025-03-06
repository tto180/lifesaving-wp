<?php
if (!defined('ABSPATH')) exit;

function lsim_debug_log($message, $data = null, $type = 'debug') {
    if (!defined('WP_DEBUG') || !WP_DEBUG) {
        return;
    }

    $output = '[LSIM ' . strtoupper($type) . '] ';
    $output .= is_array($message) || is_object($message) ? print_r($message, true) : $message;
    
    if ($data !== null) {
        $output .= "\nData: " . print_r($data, true);
    }
    
    // Add backtrace for errors
    if ($type === 'error') {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = isset($backtrace[1]) ? $backtrace[1] : $backtrace[0];
        $output .= "\nCaller: " . (isset($caller['class']) ? $caller['class'] . '::' : '') . $caller['function'];
        $output .= " (line " . $backtrace[0]['line'] . ")";
    }

    error_log($output);

    // Store errors in database for admin viewing
    if ($type === 'error') {
        $log_data = [
            'timestamp' => current_time('mysql'),
            'message' => $message,
            'data' => $data ? serialize($data) : null,
            'backtrace' => isset($backtrace) ? serialize($backtrace) : null
        ];
        
        update_option('lsim_error_log', array_slice(
            array_merge(
                [$log_data], 
                get_option('lsim_error_log', [])
            ), 
            0, 
            1000 // Keep last 1000 errors
        ));
    }
}

// Helper functions for specific log types
function lsim_log_error($message, $data = null) {
    lsim_debug_log($message, $data, 'error');
}

function lsim_log_info($message, $data = null) {
    lsim_debug_log($message, $data, 'info');
}

function lsim_log_debug($message, $data = null) {
    lsim_debug_log($message, $data, 'debug');
}