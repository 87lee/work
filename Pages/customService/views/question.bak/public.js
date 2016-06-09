//@ sourceURL=question.public.js
$(function(){
	
	function getLocalTime(nS) {       
        return new Date(parseInt(nS) * 1000).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");        
    }  
}) 