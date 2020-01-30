<?php

  if(!function_exists('handleResponse')) {
    function handleResponse(bool $success, string $message) {
      return response()->json([
        'success' => $success,
        'message' => $message
      ]);
    }
  }

  if(!function_exists('handleResponseWithData')) {
    function handleResponseWithData(bool $success, $data) {
      return response()->json([
        'success' => $success,
        'data' => $data
      ]);
    }
  }