/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function PdfView() {
    var self = this;
    var pdfJsLib = null;
    var pdfDocument = null;
    this.options = {};

    var currentPageSelector = '.pdf-view-page';
    var totalPagesSelector = '.pdf-view-total-pages';
    var pagePanelSelector = '.page-panel';
    var zoomValueSelector = '.pdf-view-page-zoom';
    var loaderSelector = '.pdf-view-loader';
    var canvasId = 'pdf_view_canvas';
  
    var currentPage = 1;
    var totalPages = 1;
    var scale = 1.0;
    var workerUrl;
    var zoomStep = 0.2;
    var maxZoom = 2.0;
    var pageRendering = false;

    this.init = function(options) {
        pdfJsLib = window['pdfjs-dist/build/pdf'];

        if (isObject(pdfJsLib) == false) {
            console.log('PDF.js library not installed');
            return false;
        }
        $('.show-popup').popup({});

        $(loaderSelector).dimmer({
            displayLoader: true
        });

        workerUrl = getValue('workerUrl',options,null);
        currentPage = getValue('page',options,currentPage);
        scale = getValue('scale',options,scale);
        canvasId = getValue('canvasId',options,canvasId);

        this.setWorkerUrl(workerUrl);
        this.options = options;
       
        $(currentPageSelector).html(currentPage);
        $(zoomValueSelector).html(this.getZoomValue());
    };

    this.getZoomValue = function() {       
        return  parseInt(scale * 100) + '%';
    };

    this.initNavbar = function() {  
        arikaim.ui.button('.pdf-view-first-page',function(element) {           
            self.render(1);
        });  
        arikaim.ui.button('.pdf-view-next-page',function(element) {           
            self.renderNextPage();
        });
        arikaim.ui.button('.pdf-view-prev-page',function(element) {          
            self.renderPrevPage();
        });
        arikaim.ui.button('.pdf-view-last-page',function(element) {           
            self.render(totalPages);
        });
        // zoom
        arikaim.ui.button('.pdf-view-zoom-plus',function(element) {           
            self.zoomIn(zoomStep);
        });
        arikaim.ui.button('.pdf-view-zoom-minus',function(element) {           
            self.zoomOut(zoomStep);
        });
    };

    this.setWorkerUrl = function(url) {
        if (isObject(pdfJsLib) == false) {
            this.init();
        }
        pdfJsLib.GlobalWorkerOptions.workerSrc = url;
    };

    this.openFile = function(url) {
        if (isObject(pdfJsLib) == false) {
            this.init();
        }
        var onError = getValue('onError',this.options,null);
        var onSuccess = getValue('onSuccess',this.options,null);

        this.showLoader();

        var task = pdfJsLib.getDocument(url);   
        
        task.promise.then(function(pdf) {
            self.hideLoader();    
            pdfDocument = pdf;

            totalPages = pdfDocument.numPages;
            $(totalPagesSelector).html(totalPages);

            $(pagePanelSelector).show();

            self.initNavbar();
            callFunction(onSuccess,pdf);           
            self.render(1);
        },function(reason) {
            // loading error      
            self.hideLoader();    
            callFunction(onError,reason);
        });     
    };

    this.showLoader = function() {
        $(loaderSelector).dimmer('show');
    };
    
    this.hideLoader = function() {
        $(loaderSelector).dimmer('hide');
    };

    this.setPage = function(page) {
        currentPage = page;
        $(currentPageSelector).html(page);
    };

    this.renderNextPage = function() {
        this.render(currentPage + 1);
    };

    this.renderPrevPage = function() {
        if ((currentPage - 1) < 1) {
            return false;
        }
        this.render(currentPage - 1);
    };

    this.zoomIn = function(inValue) {
        scale = ((scale + inValue) > maxZoom) ? maxZoom : (scale + inValue);
        $(zoomValueSelector).html(this.getZoomValue());

        this.render(); 
    }

    this.zoomOut = function(outValue) {
        if ((scale - outValue) < 0.2) {
            return false;
        }
        scale -= outValue;
        $(zoomValueSelector).html(this.getZoomValue());

        this.render(); 
    }

    this.render = function(page) {   
        if (pageRendering == true) {
            return false;
        } 
        page = getDefaultValue(page,currentPage);
       
        if (isObject(pdfDocument) == false) {
            pageRendering = false;
            console.log('Pdf file not open.');
            return false;
        }
          
        pageRendering = true;
        var onPageRendered = getValue('onPageRendered',this.options,null);
       
        self.showLoader();  
        pdfDocument.getPage(page).then(function(pdfPage) {
            var viewport = pdfPage.getViewport({ scale: scale });  
            var canvas = document.getElementById(canvasId);
            if (isEmpty(canvas) == true) {
                pageRendering = false;
                return false;
            }
            canvas.height = viewport.height;
            canvas.width = viewport.width; 
            var context = canvas.getContext('2d');                    
            var renderContext = {
                canvasContext: context,
                viewport: viewport
            };

            var task = pdfPage.render(renderContext);

            task.promise.then(function() {
                pageRendering = false;
                self.hideLoader();  
                self.setPage(page);
                callFunction(onPageRendered,page);
            });
        });
    };
}

var pdfView = new PdfView();
