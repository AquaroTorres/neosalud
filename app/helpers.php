<?php
  
function active($route_name) { 
    echo request()->routeIs($route_name) ? 'active' : '';
}
