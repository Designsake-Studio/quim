(function(c,e,k,l,a){c[e]=c[e]||{};for(c[e].q=c[e].q||[];a<l.length;)k(l[a++],c[e])})(window,"extole",function(c,e){e[c]=e[c]||function(){e.q.push([c,arguments])}},["createZone"],0);

   extole.createZone({
     name: 'global_header',
     element_id: 'extole_zone_global_header',
     data: {
        "first_name": userDetails.customerFirstName, 
        "last_name": userDetails.customerLastName, 
        "email": userDetails.customerEmail, 
     }     
  });