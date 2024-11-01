export const delay = function(cb, ms) {
	var timer = 0;

	return function () {
		var context = this, args = arguments;
    	clearTimeout(timer);
    	timer = setTimeout(function () {
      	  cb.apply(context, args);
    	}, ms || 0);
	};
}
