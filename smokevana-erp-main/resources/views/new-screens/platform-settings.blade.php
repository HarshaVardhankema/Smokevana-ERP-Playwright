@extends('layouts.app')
@section('title', 'Platform Settings')

@section('content')
    <div
        style="padding: 24px; background-color: #f8fafc; min-height: 100vh; font-family: 'Inter', system-ui, -apple-system, sans-serif;color: #1e293b;">

        <!-- Global Header -->
        <div
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; padding: 0 8px;">
            <div>
                <h1 style="font-size: 28px; font-weight: 800; color: #1e293b; margin: 0 0 4px 0;">Platform Settings</h1>
                <p style="font-size: 15px; color: #64748b; margin: 0;">Manage your marketplace identity, core engine, and
                    compliance defaults</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <button
                    style="padding: 12px 20px; border: 1px solid #e2e8f0; border-radius: 12px; background: white; font-size: 14px; font-weight: 700; color: #64748b; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-history"></i> Audit Logs
                </button>
                <button
                    style="padding: 12px 24px; background: #ea580c; color: white; border: none; border-radius: 12px; font-size: 14px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 6px -1px rgba(234, 88, 12, 0.2);">
                    <i class="fas fa-save"></i> Save All Changes
                </button>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div style="display: flex; gap: 32px; border-bottom: 1px solid #e2e8f0; margin-bottom: 32px; padding: 0 8px;">
            <a href="#"
                style="font-size: 15px; font-weight: 700; color: #f97316; text-decoration: none; padding: 12px 4px; border-bottom: 2px solid #f97316; position: relative; bottom: -1px;">General</a>
            <a href="#"
                style="font-size: 15px; font-weight: 600; color: #64748b; text-decoration: none; padding: 12px 4px;">Admin
                Team</a>
            <a href="#"
                style="font-size: 15px; font-weight: 600; color: #64748b; text-decoration: none; padding: 12px 4px;">Audit
                Logs</a>
            <a href="#"
                style="font-size: 15px; font-weight: 600; color: #64748b; text-decoration: none; padding: 12px 4px;">System
                Health</a>
            <a href="#"
                style="font-size: 15px; font-weight: 600; color: #64748b; text-decoration: none; padding: 12px 4px;">API
                Management</a>
        </div>

        <!-- Section 1: Platform Identity -->
        <div
            style="background: white; border-radius: 20px; border: 1px solid #e2e8f0; padding: 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px;">
                <div>
                    <h2 style="font-size: 22px; font-weight: 800; color: #1e293b; margin: 0 0 4px 0;">Platform Identity</h2>
                    <p style="font-size: 14px; color: #64748b; margin: 0;">Business information and branding assets</p>
                </div>
                <div style="color: #cbd5e1;"><i class="fas fa-building" style="font-size: 24px;"></i></div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 32px;">
                <div>
                    <label
                        style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 10px;">Business
                        Name</label>
                    <input type="text" value="Smokevana Marketplace"
                        style="width: 100%; padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 15px; outline: none; color: #1e293b; font-weight: 600; background: #fff;">
                </div>
                <div>
                    <label
                        style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 10px;">Support
                        Email</label>
                    <input type="email" value="support@smokevana.com"
                        style="width: 100%; padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 15px; outline: none; color: #1e293b; font-weight: 600; background: #fff;">
                </div>
                <div>
                    <label
                        style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 10px;">Support
                        Phone</label>
                    <input type="text" value="+1 (555) 123-4567"
                        style="width: 100%; padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 15px; outline: none; color: #1e293b; font-weight: 600; background: #fff;">
                </div>
                <div>
                    <label
                        style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 10px;">Business
                        Address</label>
                    <input type="text" value="123 Commerce Blvd, Suite 400, Denver CO 80202"
                        style="width: 100%; padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 15px; outline: none; color: #1e293b; font-weight: 600; background: #fff;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 40px;">
                <div>
                    <label
                        style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 12px;">Platform
                        Logo</label>
                    <div
                        style="border: 1px dashed #cbd5e1; border-radius: 12px; padding: 48px; text-align: center; background: #fff;">
                        <div
                            style="background: #f97316; width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; color: white;">
                            <i class="fas fa-leaf" style="font-size: 32px;"></i>
                        </div>
                        <div style="font-size: 13px; color: #64748b; margin-bottom: 12px; font-weight: 600;">Current logo
                            (200x200)</div>
                        <button
                            style="background: none; border: none; color: #f97316; font-size: 14px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;">
                            <i class="fas fa-upload"></i> Upload New Logo
                        </button>
                    </div>
                </div>
                <div>
                    <label
                        style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 12px;">Favicon</label>
                    <div
                        style="border: 1px dashed #cbd5e1; border-radius: 12px; padding: 48px; text-align: center; background: #fff;">
                        <div
                            style="background: #fff7ed; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 10px auto 26px auto; color: #f97316; border: 1px solid #ffedd5;">
                            <i class="fas fa-leaf" style="font-size: 20px;"></i>
                        </div>
                        <div style="font-size: 13px; color: #64748b; margin-bottom: 12px; font-weight: 600;">Current favicon
                            (32x32)</div>
                        <button
                            style="background: none; border: none; color: #f97316; font-size: 14px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;">
                            <i class="fas fa-upload"></i> Upload New Favicon
                        </button>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 32px;">
                <button
                    style="padding: 12px 28px; background: #f97316; color: white; border: none; border-radius: 10px; font-size: 15px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 6px -1px rgba(249, 115, 22, 0.2);">
                    <i class="fas fa-save"></i> Save Identity Settings
                </button>
            </div>
        </div>

        <!-- Section 2: Marketplace Settings -->
        <div
            style="background: white; border-radius: 20px; border: 1px solid #e2e8f0; padding: 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px;">
                <div>
                    <h2 style="font-size: 22px; font-weight: 800; color: #1e293b; margin: 0 0 4px 0;">Marketplace Settings
                    </h2>
                    <p style="font-size: 14px; color: #64748b; margin: 0;">Control marketplace availability and registration
                    </p>
                </div>
                <div
                    style="width: 44px; height: 24px; background: #e2e8f0; border-radius: 20px; position: relative; cursor: pointer;">
                    <div
                        style="position: absolute; left: 2px; top: 2px; width: 20px; height: 20px; background: white; border-radius: 50%;">
                    </div>
                </div>
            </div>

            <!-- Toggles List -->
            <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 40px;">

                <!-- Marketplace Open -->
                <div
                    style="background: #f0fdf4; border: 1px solid #dcfce7; border-radius: 12px; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <div
                            style="background: #dcfce7; color: #16a34a; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-power-off"></i>
                        </div>
                        <div>
                            <div style="font-size: 15px; font-weight: 800; color: #1e293b;">Marketplace Open</div>
                            <div style="font-size: 13px; color: #64748b;">Master kill switch - controls entire platform
                                availability</div>
                        </div>
                    </div>
                    <div
                        style="width: 44px; height: 24px; background: #16a34a; border-radius: 20px; position: relative; cursor: pointer;">
                        <div
                            style="position: absolute; right: 2px; top: 2px; width: 20px; height: 20px; background: white; border-radius: 50%;">
                        </div>
                    </div>
                </div>

                <!-- New Seller Registration -->
                <div
                    style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <div
                            style="background: #eff6ff; color: #3b82f6; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-store"></i>
                        </div>
                        <div>
                            <div style="font-size: 15px; font-weight: 800; color: #1e293b;">New Seller Registration</div>
                            <div style="font-size: 13px; color: #64748b;">Allow new sellers to sign up and create stores
                            </div>
                        </div>
                    </div>
                    <div
                        style="width: 44px; height: 24px; background: #f97316; border-radius: 20px; position: relative; cursor: pointer;">
                        <div
                            style="position: absolute; right: 2px; top: 2px; width: 20px; height: 20px; background: white; border-radius: 50%;">
                        </div>
                    </div>
                </div>

                <!-- New Buyer Registration -->
                <div
                    style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <div
                            style="background: #f5f3ff; color: #8b5cf6; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <div style="font-size: 15px; font-weight: 800; color: #1e293b;">New Buyer Registration</div>
                            <div style="font-size: 13px; color: #64748b;">Allow new buyers to create accounts and shop</div>
                        </div>
                    </div>
                    <div
                        style="width: 44px; height: 24px; background: #f97316; border-radius: 20px; position: relative; cursor: pointer;">
                        <div
                            style="position: absolute; right: 2px; top: 2px; width: 20px; height: 20px; background: white; border-radius: 50%;">
                        </div>
                    </div>
                </div>

                <!-- FBS Accepting Inbound -->
                <div
                    style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <div
                            style="background: #eff6ff; color: #3b82f6; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-truck-loading"></i>
                        </div>
                        <div>
                            <div style="font-size: 15px; font-weight: 800; color: #1e293b;">FBS Accepting Inbound</div>
                            <div style="font-size: 13px; color: #64748b;">Fulfillment by Smokevana accepting new inventory
                            </div>
                        </div>
                    </div>
                    <div
                        style="width: 44px; height: 24px; background: #f97316; border-radius: 20px; position: relative; cursor: pointer;">
                        <div
                            style="position: absolute; right: 2px; top: 2px; width: 20px; height: 20px; background: white; border-radius: 50%;">
                        </div>
                    </div>
                </div>

                <!-- Advertising Platform Active -->
                <div
                    style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <div
                            style="background: #fffbeb; color: #f59e0b; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div>
                            <div style="font-size: 15px; font-weight: 800; color: #1e293b;">Advertising Platform Active
                            </div>
                            <div style="font-size: 13px; color: #64748b;">Enable sponsored products and ad campaigns</div>
                        </div>
                    </div>
                    <div
                        style="width: 44px; height: 24px; background: #f97316; border-radius: 20px; position: relative; cursor: pointer;">
                        <div
                            style="position: absolute; right: 2px; top: 2px; width: 20px; height: 20px; background: white; border-radius: 50%;">
                        </div>
                    </div>
                </div>

                <!-- Maintenance Mode -->
                <div
                    style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <div
                            style="background: #fee2e2; color: #ef4444; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div>
                            <div style="font-size: 15px; font-weight: 800; color: #1e293b;">Maintenance Mode</div>
                            <div style="font-size: 13px; color: #ef4444; font-weight: 700;"><i
                                    class="fas fa-exclamation-triangle" style="margin-right: 4px;"></i> WARNING: Activating
                                will show maintenance page to all users</div>
                        </div>
                    </div>
                    <div
                        style="width: 44px; height: 24px; background: #e2e8f0; border-radius: 20px; position: relative; cursor: pointer;">
                        <div
                            style="position: absolute; left: 2px; top: 2px; width: 20px; height: 20px; background: white; border-radius: 50%;">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Regional Settings -->
            <div
                style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; border-top: 1px solid #f1f5f9; padding-top: 32px;">
                <div>
                    <label
                        style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 10px;">Default
                        Currency</label>
                    <div style="position: relative;">
                        <select
                            style="width: 100%; padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 15px; outline: none; appearance: none; background: #f8fafc; color: #1e293b; font-weight: 600;">
                            <option>USD ($)</option>
                        </select>
                        <i class="fas fa-chevron-down"
                            style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 12px; pointer-events: none;"></i>
                    </div>
                </div>
                <div>
                    <label
                        style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 10px;">Timezone</label>
                    <div style="position: relative;">
                        <select
                            style="width: 100%; padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 15px; outline: none; appearance: none; background: #f8fafc; color: #1e293b; font-weight: 600;">
                            <option>America/Denver (MST)</option>
                        </select>
                        <i class="fas fa-chevron-down"
                            style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 12px; pointer-events: none;"></i>
                    </div>
                </div>
                <div>
                    <label
                        style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 10px;">Date
                        Format</label>
                    <div style="position: relative;">
                        <select
                            style="width: 100%; padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 15px; outline: none; appearance: none; background: #f8fafc; color: #1e293b; font-weight: 600;">
                            <option>MM/DD/YYYY</option>
                        </select>
                        <i class="fas fa-chevron-down"
                            style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 12px; pointer-events: none;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Compliance Defaults -->
        <div
            style="background: white; border-radius: 20px; border: 1px solid #e2e8f0; padding: 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px;">
                <div>
                    <h2 style="font-size: 22px; font-weight: 800; color: #1e293b; margin: 0 0 4px 0;">Compliance Defaults
                    </h2>
                    <p style="font-size: 14px; color: #64748b; margin: 0;">Platform-wide compliance and regulatory settings
                    </p>
                </div>
                <div style="color: #cbd5e1;"><i class="fas fa-shield-alt" style="font-size: 24px;"></i></div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 32px;">
                <!-- Row 1 Left: Age Verification -->
                <div>
                    <label
                        style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 12px;">Default
                        Age Verification Minimum</label>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                        <input type="text" value="21"
                            style="width: 80px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 16px; font-weight: 800; text-align: center; color: #1e293b; outline: none; background: #fff;">
                        <span style="font-size: 14px; font-weight: 600; color: #64748b;">years old</span>
                    </div>
                    <div style="font-size: 12px; color: #94a3b8; font-weight: 500;">Federal minimum for cannabis purchases
                    </div>
                </div>

                <!-- Row 1 Right: COA Maximum Age -->
                <div>
                    <label
                        style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 12px;">Default
                        COA Maximum Age</label>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                        <input type="text" value="18"
                            style="width: 80px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 16px; font-weight: 800; text-align: center; color: #1e293b; outline: none; background: #fff;">
                        <span style="font-size: 14px; font-weight: 600; color: #64748b;">months</span>
                    </div>
                    <div style="font-size: 12px; color: #94a3b8; font-weight: 500;">Certificate of Analysis validity period
                    </div>
                </div>

                <!-- Row 2 Left: Auto-Suspension -->
                <div>
                    <label
                        style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 12px;">Compliance
                        Score Auto-Suspension Threshold</label>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                        <input type="text" value="65"
                            style="width: 80px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 16px; font-weight: 800; text-align: center; color: #1e293b; outline: none; background: #fff;">
                        <span style="font-size: 14px; font-weight: 600; color: #64748b;">% or below</span>
                    </div>
                    <div style="font-size: 12px; color: #94a3b8; font-weight: 500;">Sellers below this score are
                        automatically suspended</div>
                </div>

                <!-- Row 2 Right: PACT Act Reporting -->
                <div
                    style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 15px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">PACT Act
                            Reporting Auto-Generation</div>
                        <div style="font-size: 13px; color: #64748b;">Automatically generate compliance reports</div>
                    </div>
                    <div
                        style="width: 44px; height: 24px; background: #f97316; border-radius: 20px; position: relative; cursor: pointer;">
                        <div
                            style="position: absolute; right: 2px; top: 2px; width: 20px; height: 20px; background: white; border-radius: 50%;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Auto-Suppress Box -->
            <div
                style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 16px; padding: 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
                <div style="display: flex; align-items: center; gap: 20px;">
                    <div
                        style="background: #fef3c7; color: #d97706; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <div style="font-size: 16px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">Auto-Suppress
                            Products with Expired COAs</div>
                        <div style="font-size: 14px; color: #64748b;">Automatically hide products when COA expires</div>
                    </div>
                </div>
                <div
                    style="width: 44px; height: 24px; background: #f97316; border-radius: 20px; position: relative; cursor: pointer;">
                    <div
                        style="position: absolute; right: 2px; top: 2px; width: 20px; height: 20px; background: white; border-radius: 50%;">
                    </div>
                </div>
            </div>

            <!-- Blue Requirement Box -->
            <div
                style="background: #eff6ff; border: 1px solid #dbeafe; border-radius: 12px; padding: 24px; display: flex; gap: 20px; align-items: flex-start;">
                <div
                    style="color: #3b82f6; background: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 10px;">
                    <i class="fas fa-info"></i>
                </div>
                <div>
                    <h4 style="font-size: 15px; font-weight: 800; color: #1e3a8a; margin: 0 0 6px 0;">Compliance
                        Requirements</h4>
                    <p style="font-size: 14px; color: #1e40af; margin: 0; line-height: 1.6; font-weight: 500;">
                        All sellers must maintain valid Certificates of Analysis (COA) for their products. Products without
                        current COAs will be automatically hidden from the marketplace to ensure regulatory compliance.
                    </p>
                </div>
            </div>
        </div>

    </div>
@endsection