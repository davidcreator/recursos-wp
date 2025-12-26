#!/usr/bin/env python3
"""
Script para encontrar erros de sintaxe no plugin WordPress
Uso: python find_error.py caminho/para/ai-post-generator.php
"""

import sys
import re

def check_php_syntax(filepath):
    print("=" * 60)
    print("Analisador de Sintaxe PHP")
    print("=" * 60)
    print()
    
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            lines = f.readlines()
    except FileNotFoundError:
        print(f"❌ Arquivo não encontrado: {filepath}")
        return
    
    # Contadores
    open_braces = 0
    open_parens = 0
    open_brackets = 0
    in_function = False
    function_stack = []
    
    # Rastreamento
    brace_lines = []
    
    print(f"📁 Analisando: {filepath}")
    print(f"📊 Total de linhas: {len(lines)}")
    print()
    
    errors = []
    warnings = []
    
    for i, line in enumerate(lines, 1):
        line_num = i
        stripped = line.strip()
        
        # Ignora comentários
        if stripped.startswith('//') or stripped.startswith('#'):
            continue
        if stripped.startswith('/*') or stripped.startswith('*'):
            continue
            
        # Conta chaves
        for char in line:
            if char == '{':
                open_braces += 1
                brace_lines.append((line_num, 'open', open_braces))
            elif char == '}':
                open_braces -= 1
                brace_lines.append((line_num, 'close', open_braces))
                
                if open_braces < 0:
                    errors.append(f"Linha {line_num}: Chave de fechamento sem abertura correspondente")
                    
            elif char == '(':
                open_parens += 1
            elif char == ')':
                open_parens -= 1
                if open_parens < 0:
                    errors.append(f"Linha {line_num}: Parêntese de fechamento sem abertura")
                    
            elif char == '[':
                open_brackets += 1
            elif char == ']':
                open_brackets -= 1
                if open_brackets < 0:
                    errors.append(f"Linha {line_num}: Colchete de fechamento sem abertura")
        
        # Detecta início de função
        if re.search(r'(public|private|protected)?\s*function\s+\w+\s*\(', stripped):
            match = re.search(r'function\s+(\w+)', stripped)
            if match:
                func_name = match.group(1)
                function_stack.append((line_num, func_name))
                in_function = True
        
        # Verifica linha 1078 especificamente
        if line_num == 1078:
            print(f"🔍 LINHA 1078 (onde o erro ocorre):")
            print(f"   Conteúdo: {line.rstrip()}")
            print(f"   Chaves abertas neste ponto: {open_braces}")
            print(f"   Última função declarada: ", end="")
            if function_stack:
                print(f"{function_stack[-1][1]} (linha {function_stack[-1][0]})")
            else:
                print("Nenhuma")
            print()
            
            # Mostra contexto
            print("   Contexto (10 linhas antes):")
            for j in range(max(0, line_num - 11), line_num):
                context_line = lines[j]
                print(f"   {j+1:4d}: {context_line.rstrip()}")
            print()
    
    # Resultados finais
    print("=" * 60)
    print("RESULTADOS:")
    print("=" * 60)
    print()
    
    if open_braces != 0:
        if open_braces > 0:
            errors.append(f"⚠️  {open_braces} chave(s) de abertura sem fechamento")
        else:
            errors.append(f"⚠️  {abs(open_braces)} chave(s) de fechamento sem abertura")
    else:
        print("✅ Chaves balanceadas")
    
    if open_parens != 0:
        warnings.append(f"⚠️  {abs(open_parens)} parêntese(s) desbalanceado(s)")
    else:
        print("✅ Parênteses balanceados")
    
    if open_brackets != 0:
        warnings.append(f"⚠️  {abs(open_brackets)} colchete(s) desbalanceado(s)")
    else:
        print("✅ Colchetes balanceados")
    
    print()
    
    if errors:
        print("❌ ERROS ENCONTRADOS:")
        for error in errors:
            print(f"   {error}")
        print()
    
    if warnings:
        print("⚠️  AVISOS:")
        for warning in warnings:
            print(f"   {warning}")
        print()
    
    # Análise de chaves
    print("📊 Histórico de Chaves (últimas 20):")
    for line_num, action, count in brace_lines[-20:]:
        symbol = '{' if action == 'open' else '}'
        print(f"   Linha {line_num:4d}: {symbol} (total: {count})")
    print()
    
    # Funções não fechadas
    if function_stack and open_braces > 0:
        print("🔍 Possíveis funções não fechadas:")
        for line_num, func_name in function_stack[-5:]:
            print(f"   Linha {line_num}: function {func_name}()")
        print()
    
    print("=" * 60)
    
    if not errors and not warnings:
        print("✅ Nenhum erro óbvio encontrado!")
        print("   O erro pode estar na sintaxe específica do PHP.")
        print("   Execute: php -l ai-post-generator.php")
    else:
        print("❌ Corrija os erros acima e tente novamente.")
    
    print("=" * 60)

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Uso: python find_error.py caminho/para/ai-post-generator.php")
        sys.exit(1)
    
    check_php_syntax(sys.argv[1])