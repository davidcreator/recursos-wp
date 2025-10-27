/* AdSense Master Pro - Admin JavaScript */

jQuery(document).ready(function($) {
    'use strict';
    
    // Variáveis globais
    var currentEditingId = null;
    var codeEditor = null;
    
    // Inicialização
    init();
    
    function init() {
        bindEvents();
        initializeCodeEditor();
        setupFormValidation();
    }
    
    function bindEvents() {
        // Adicionar novo anúncio
        $(document).on('click', '#add-new-ad', function(e) {
            e.preventDefault();
            showAdEditor();
            resetForm();
        });
        
        // Cancelar edição
        $(document).on('click', '#cancel-edit', function(e) {
            e.preventDefault();
            hideAdEditor();
        });
        
        // Editar anúncio
        $(document).on('click', '.edit-ad', function(e) {
            e.preventDefault();
            var adId = $(this).data('id');
            loadAdForEdit(adId);
        });
        
        // Excluir anúncio
        $(document).on('click', '.delete-ad', function(e) {
            e.preventDefault();
            var adId = $(this).data('id');
            deleteAd(adId);