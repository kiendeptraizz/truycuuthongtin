@extends('layouts.admin')

@section('title', 'Demo Icon System')
@section('page-title', 'Icon System Demo')

@push('styles')
<style>
    .demo-section {
        margin: 30px 0;
        padding: 25px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        background: #f8f9fa;
    }
    
    .demo-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin: 20px 0;
    }
    
    .icon-demo {
        font-size: 1.2em;
        margin-right: 8px;
    }
    
    .status-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
    }
    
    .status-working { background: #28a745; }
    .status-fallback { background: #ffc107; }
    .status-broken { background: #dc3545; }
    
    .console-output {
        background: #1e1e1e;
        color: #d4d4d4;
        padding: 20px;
        border-radius: 8px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
        line-height: 1.4;
        max-height: 300px;
        overflow-y: auto;
        white-space: pre-wrap;
    }
    
    .icon-test-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin: 20px 0;
    }
    
    .icon-test-card {
        padding: 15px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: white;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-cogs icon-demo"></i>
                        Icon System Comprehensive Test
                    </h4>
                    <small class="opacity-75">Kiểm tra và debug hệ thống icon Font Awesome + Emoji fallback</small>
                </div>
                
                <div class="card-body">
                    <!-- System Status -->
                    <div class="demo-section">
                        <h5><i class="fas fa-info-circle icon-demo"></i>System Status</h5>
                        <div id="system-status">
                            <div class="d-flex align-items-center mb-2">
                                <span class="status-indicator status-working"></span>
                                <span>Font Awesome Loading: <span id="fa-status">Checking...</span></span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="status-indicator status-fallback"></span>
                                <span>Emoji Fallbacks: <span id="fallback-count">0</span> active</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="status-indicator status-broken"></span>
                                <span>Broken Icons: <span id="broken-count">0</span> detected</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Control Panel -->
                    <div class="demo-section">
                        <h5><i class="fas fa-sliders-h icon-demo"></i>Control Panel</h5>
                        <div class="demo-buttons">
                            <button class="btn btn-primary" onclick="runIconDiagnostic()">
                                <i class="fas fa-search icon-demo"></i>Run Diagnostic
                            </button>
                            <button class="btn btn-warning" onclick="forceApplyFallbacks()">
                                <i class="fas fa-tools icon-demo"></i>Force Apply Fallbacks
                            </button>
                            <button class="btn btn-info" onclick="toggleDebugMode()">
                                <i class="fas fa-bug icon-demo"></i>Toggle Debug
                            </button>
                            <button class="btn btn-success" onclick="refreshPage()">
                                <i class="fas fa-sync icon-demo"></i>Refresh Test
                            </button>
                            <button class="btn btn-secondary" onclick="clearConsole()">
                                <i class="fas fa-eraser icon-demo"></i>Clear Console
                            </button>
                        </div>
                    </div>
                    
                    <!-- Icon Test Grid -->
                    <div class="demo-section">
                        <h5><i class="fas fa-th icon-demo"></i>Icon Test Grid</h5>
                        <div class="icon-test-grid">
                            <!-- Action Buttons -->
                            <div class="icon-test-card">
                                <h6>Action Buttons</h6>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-eye icon-demo">👁️</i>
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-edit icon-demo">✏️</i>
                                    </button>
                                    <button class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-plus icon-demo">➕</i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash icon-demo">🗑️</i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Toggle Buttons -->
                            <div class="icon-test-card">
                                <h6>Toggle States</h6>
                                <button class="btn btn-success btn-sm me-2">
                                    <i class="fas fa-toggle-on icon-demo">🟢</i>
                                </button>
                                <button class="btn btn-secondary btn-sm">
                                    <i class="fas fa-toggle-off icon-demo">⭕</i>
                                </button>
                            </div>
                            
                            <!-- Navigation Icons -->
                            <div class="icon-test-card">
                                <h6>Navigation</h6>
                                <div class="d-flex justify-content-around">
                                    <i class="fas fa-home icon-demo">🏠</i>
                                    <i class="fas fa-users icon-demo">👥</i>
                                    <i class="fas fa-cog icon-demo">⚙️</i>
                                    <i class="fas fa-chart-bar icon-demo">📊</i>
                                </div>
                            </div>
                            
                            <!-- Status Icons -->
                            <div class="icon-test-card">
                                <h6>Status Icons</h6>
                                <div class="d-flex justify-content-around">
                                    <i class="fas fa-check text-success icon-demo">✅</i>
                                    <i class="fas fa-times text-danger icon-demo">❌</i>
                                    <i class="fas fa-exclamation-triangle text-warning icon-demo">⚠️</i>
                                    <i class="fas fa-info-circle text-info icon-demo">ℹ️</i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Real-world Examples -->
                    <div class="demo-section">
                        <h5><i class="fas fa-table icon-demo"></i>Real-world Table Actions</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Test Item 1</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="#" class="btn btn-outline-info btn-sm" title="View">
                                                    <i class="fas fa-eye" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;">👁️</i>
                                                </a>
                                                <a href="#" class="btn btn-outline-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;">✏️</i>
                                                </a>
                                                <button class="btn btn-outline-danger btn-sm" title="Delete">
                                                    <i class="fas fa-trash" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;">🗑️</i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Test Item 2</td>
                                        <td><span class="badge bg-secondary">Inactive</span></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="#" class="btn btn-outline-info btn-sm" title="View">
                                                    <i class="fas fa-eye" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;">👁️</i>
                                                </a>
                                                <a href="#" class="btn btn-outline-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;">✏️</i>
                                                </a>
                                                <button class="btn btn-outline-danger btn-sm" title="Delete">
                                                    <i class="fas fa-trash" style="font-family: 'Font Awesome 6 Free'; font-weight: 900;">🗑️</i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Debug Console -->
                    <div class="demo-section">
                        <h5><i class="fas fa-terminal icon-demo"></i>Debug Console</h5>
                        <div id="debug-console" class="console-output">
                            <div>🔍 Icon System Debug Console</div>
                            <div>=============================================</div>
                            <div>Waiting for diagnostic results...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let debugMode = false;

function logToConsole(message) {
    const console = document.getElementById('debug-console');
    const timestamp = new Date().toLocaleTimeString();
    console.innerHTML += `\n[${timestamp}] ${message}`;
    console.scrollTop = console.scrollHeight;
}

function clearConsole() {
    document.getElementById('debug-console').innerHTML = 
        '🔍 Icon System Debug Console\n' +
        '=============================================\n' +
        'Console cleared...';
}

function runIconDiagnostic() {
    logToConsole('🔍 Starting comprehensive icon diagnostic...');
    
    // Test Font Awesome loading
    setTimeout(() => {
        const testElement = document.createElement('i');
        testElement.className = 'fas fa-heart';
        testElement.style.cssText = 'position: absolute; left: -9999px; visibility: hidden;';
        document.body.appendChild(testElement);
        
        setTimeout(() => {
            const style = window.getComputedStyle(testElement, ':before');
            const content = style.getPropertyValue('content');
            const fontFamily = style.getPropertyValue('font-family');
            const isLoaded = content && content !== 'none' && content !== '""' && content !== 'normal';
            
            document.body.removeChild(testElement);
            
            logToConsole(`📊 Font Awesome Status: ${isLoaded ? 'LOADED ✅' : 'FAILED ❌'}`);
            logToConsole(`📊 Font Family: ${fontFamily}`);
            logToConsole(`📊 CSS Content: ${content}`);
            
            document.getElementById('fa-status').textContent = isLoaded ? 'Loaded ✅' : 'Failed ❌';
            
            // Count icons
            const allIcons = document.querySelectorAll('[class*="fa-"]');
            const fallbackIcons = document.querySelectorAll('[data-icon-fallback="true"]');
            let brokenIcons = 0;
            
            allIcons.forEach(icon => {
                const hasText = icon.textContent.trim().length > 0;
                const style = window.getComputedStyle(icon, ':before');
                const beforeContent = style.getPropertyValue('content');
                const hasBefore = beforeContent && beforeContent !== 'none' && beforeContent !== '""';
                
                if (!hasText && !hasBefore) {
                    brokenIcons++;
                }
            });
            
            document.getElementById('fallback-count').textContent = fallbackIcons.length;
            document.getElementById('broken-count').textContent = brokenIcons;
            
            logToConsole(`📊 Icon Statistics:`);
            logToConsole(`   Total Icons: ${allIcons.length}`);
            logToConsole(`   Fallback Icons: ${fallbackIcons.length}`);
            logToConsole(`   Broken Icons: ${brokenIcons}`);
            logToConsole(`   Working Icons: ${allIcons.length - fallbackIcons.length - brokenIcons}`);
            
        }, 100);
    }, 100);
}

function forceApplyFallbacks() {
    logToConsole('🔧 Force applying emoji fallbacks...');
    
    if (window.iconManager) {
        window.iconManager.forceFixAll();
        logToConsole('✅ Icon Manager fallbacks applied');
    } else {
        logToConsole('❌ Icon Manager not available');
    }
    
    setTimeout(runIconDiagnostic, 500);
}

function toggleDebugMode() {
    debugMode = !debugMode;
    
    if (window.iconManager) {
        if (debugMode) {
            window.iconManager.enableDebug();
            logToConsole('🐛 Debug mode ENABLED');
        } else {
            window.iconManager.disableDebug();
            logToConsole('🐛 Debug mode DISABLED');
        }
    }
}

function refreshPage() {
    logToConsole('🔄 Refreshing page...');
    setTimeout(() => location.reload(), 1000);
}

// Auto-run diagnostic when page loads
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(runIconDiagnostic, 1000);
    
    // Auto-refresh stats every 5 seconds
    setInterval(() => {
        if (debugMode) {
            runIconDiagnostic();
        }
    }, 5000);
});
</script>
@endpush
