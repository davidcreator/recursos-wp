#!/usr/bin/env python3
"""
Script para encontrar onde está faltando a chave de abertura
"""

import sys
import re

def find_missing_brace(filepath):
    print("=" * 60)
    print("Buscando chave faltante...")
    print("=" * 60)
    print()
    
    with open(filepath, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    open_braces = 0
    balance_history = []
    
    for i, line in enumerate(lines, 1):
        line_stripped = line.strip()
        
        # Conta chaves nesta linha
        opens_in_line = line.count('{')
        closes_in_line = line.count('}')
        
        for char in line:
            if char == '{':
                open_braces += 1
            elif char == '}':
                open_braces -= 1
                
                if open_braces < 0:
                    # Encontrou o primeiro desequilíbrio
                    print(f"❌ Primeira chave desbalanceada na linha {i}")
                    print()
                    
                    # Mostra contexto
                    print("Contexto (20 linhas antes):")
                    for j in range(max(0, i-20), i):
                        marker = ">>>" if j+1 == i else "   "
                        print(f"{marker} {j+1:4d}: {lines[j].rstrip()}")
                    print()
                    
                    # Procura última função antes desta linha
                    print("Buscando funções anteriores...")
                    for j in range(i-1, max(0, i-100), -1):
                        if re.search(r'(public|private|protected)?\s*function\s+\w+', lines[j]):
                            print(f"   Linha {j+1}: {lines[j].strip()}")
                    print()
                    
                    return i
        
        balance_history.append((i, open_braces, opens_in_line, closes_in_line))
    
    print("Histórico de balanço das chaves:")
    print()
    print("Linha | Balanço | Abre | Fecha | Conteúdo")
    print("-" * 80)
    
    # Mostra linhas problemáticas
    for line_num, balance, opens, closes in balance_history:
        if opens > 0 or closes > 0 or balance < 0:
            content = lines[line_num-1].strip()[:50]
            print(f"{line_num:4d} | {balance:+7d} | {opens:4d} | {closes:5d} | {content}")
    
    print()
    print("=" * 60)

def suggest_fixes(filepath):
    """Sugere onde adicionar a chave faltante"""
    
    print("🔍 Analisando métodos...")
    print()
    
    with open(filepath, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    methods = []
    current_method = None
    brace_count = 0
    
    for i, line in enumerate(lines, 1):
        # Detecta início de método
        if re.search(r'(public|private|protected)\s+function\s+(\w+)', line):
            match = re.search(r'function\s+(\w+)', line)
            if match:
                if current_method:
                    methods.append(current_method)
                current_method = {
                    'name': match.group(1),
                    'start': i,
                    'end': None,
                    'brace_balance': 0
                }
        
        # Conta chaves
        if current_method:
            opens = line.count('{')
            closes = line.count('}')
            current_method['brace_balance'] += opens - closes
            
            # Se fechou completamente
            if current_method['brace_balance'] == 0 and opens + closes > 0:
                current_method['end'] = i
                methods.append(current_method)
                current_method = None
    
    # Verifica métodos sem fechamento
    print("Métodos analisados:")
    print()
    
    for method in methods[-10:]:  # Últimos 10 métodos
        status = "✅" if method['end'] else "❌ SEM FECHAMENTO"
        end_info = f"até linha {method['end']}" if method['end'] else "NÃO FECHADO"
        print(f"{status} {method['name']}() - Linha {method['start']} {end_info}")
    
    print()
    
    # Encontra método problemático
    for method in methods:
        if not method['end'] and method['start'] < 1695:
            print(f"⚠️  POSSÍVEL PROBLEMA:")
            print(f"   Método: {method['name']}()")
            print(f"   Linha: {method['start']}")
            print(f"   Este método pode não ter sido fechado!")
            print()
            
            # Mostra o método
            print(f"Conteúdo a partir da linha {method['start']}:")
            for j in range(method['start']-1, min(method['start']+30, len(lines))):
                print(f"   {j+1:4d}: {lines[j].rstrip()}")
            print()

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Uso: python fix_missing_brace.py ai-post-generator.php")
        sys.exit(1)
    
    filepath = sys.argv[1]
    
    first_error = find_missing_brace(filepath)
    print()
    suggest_fixes(filepath)
    
    print()
    print("=" * 60)
    print("RECOMENDAÇÃO:")
    print("=" * 60)
    print()
    print("1. Verifique os métodos sem fechamento listados acima")
    print("2. Adicione } no final do método problemático")
    print("3. Execute novamente este script para verificar")
    print()