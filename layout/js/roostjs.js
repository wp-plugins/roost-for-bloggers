    var _roost = _roost || [];
    _roost.push(['appkey', roostjsParams.appkey]);

    !function(d,s,id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if(!d.getElementById(id)){
            js=d.createElement(s); js.id=id;
            js.src='//get.roost.me/js/roost.js';
            fjs.parentNode.insertBefore(js,fjs);
        }
    }(document, 'script', 'roost-js');