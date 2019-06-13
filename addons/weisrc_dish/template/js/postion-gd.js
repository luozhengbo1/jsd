$(function() {
    function distanceByLnglat(lng1, lat1, lng2, lat2) {
        var radLat1 = Rad(lat1);
        var radLat2 = Rad(lat2);
        var a = radLat1 - radLat2;
        var b = Rad(lng1) - Rad(lng2);
        var s = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(a / 2), 2) + Math.cos(radLat1) * Math.cos(radLat2) * Math.pow(Math.sin(b / 2), 2)));
        s = s * 6378137.0;
        s = Math.round(s * 10000) / 10000000;
        s = s.toFixed(2);
        return s;
    }
    function Rad(d) {
        return d * Math.PI / 180.0
    };

    var map = new AMap.Map('container', {
        resizeEnable: true
    });
    AMap.plugin('AMap.Geolocation', function() {
        var geolocation = new AMap.Geolocation({
            enableHighAccuracy: true,//是否使用高精度定位，默认:true
            timeout: 10000,          //超过10秒后停止定位，默认：5s
            buttonPosition:'RB',    //定位按钮的停靠位置
            buttonOffset: new AMap.Pixel(10, 20),//定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
            zoomToAccuracy: true,   //定位成功后是否自动调整地图视野到定位点

        });
        map.addControl(geolocation);
        geolocation.getCurrentPosition(function(status,result){
            if(status=='complete'){
                onComplete(result)
                console.log('定位成功')
            }else{
                // onError(result)
                onComplete(result)
                console.log('定位失败')
            }
        });
    });
    //解析定位结果
    function onComplete(data) {
        var locLng ,locLat
        // console.log( data.position)
        locLng = data.position.lng;
        locLat = data.position.lat;
        // locLng = 106.64438;
        // locLat = 26.61859;
        $(".morelist").each(function() {
            var ShopLngLat = $(this).find("#showlan").val();
            console.log(ShopLngLat)
            var InputOF = ShopLngLat.indexOf(",");
            var InputOFLast = ShopLngLat.length;
            var ShopLng = ShopLngLat.slice(0, InputOF);
            var ShopLat = ShopLngLat.slice(InputOF + 1, InputOFLast);
            var dis111 = distanceByLnglat(locLng, locLat, ShopLng, ShopLat);
            $(this).find("#shopspostion").html(dis111 + "km");
        });
        $("#curlat").val(locLat);
        $("#curlng").val(locLng);

        isposition = $("#isposition").val();
        cururl = $("#cururl").val();
        if (isposition == 0) {
            var url = cururl + '&lat=' + locLat + '&lng=' + locLng + '&pos=1';
            window.location = url;
        }
    }
    //解析定位错误信息
    function onError(data) {
        $(".morelist").each(function() {
            $(this).find("#shopspostion").html("无法获取距离" + _this.getStatus() + "");
        });
    }

});

