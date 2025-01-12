@extends('layouts.theme.app')
@section('content')
<style>
  .table-totales {
    /*border: 2px solid red;*/
  }

  .table-totales,
  th,
  td {
    border: 1px solid #DCDCDC;
  }

  .table-inventario,
  th,
  td {
    border: 1px solid #DCDCDC;
  }

  .tabs {
    list-style-type: none;
    /* Remove default list styles */
    padding: 0;
    /* Remove default padding */
    margin: 0;
    /* Remove default margin */
  }

  .tabs li {
    margin: 0 10px;
    /* Add some margin between the tabs */
  }
</style>
