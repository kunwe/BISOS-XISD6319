package com.yourcompany.informalsupermarket.util

import java.net.URLEncoder

object AiImageGenerator {

    /**
     * Intelligently parses product names containing measurements (e.g. 2L, 1kg, 750ml, 500mg, 700g, 30-pack)
     * and formats them into descriptive AI image prompts.
     */
    fun buildSmartPrompt(productName: String): String {
        val name = productName.trim().ifBlank { "grocery product" }

        // Detect measurement units in name using Regex
        val isLiquidL = Regex("(?i)\\b(\\d+(?:\\.\\d+)?)\\s*l\\b").find(name)
        val isLiquidMl = Regex("(?i)\\b(\\d+(?:\\.\\d+)?)\\s*ml\\b").find(name)
        val isWeightKg = Regex("(?i)\\b(\\d+(?:\\.\\d+)?)\\s*kg\\b").find(name)
        val isWeightG = Regex("(?i)\\b(\\d+(?:\\.\\d+)?)\\s*g\\b").find(name)
        val isWeightMg = Regex("(?i)\\b(\\d+(?:\\.\\d+)?)\\s*mg\\b").find(name)
        val isPack = Regex("(?i)\\b(\\d+)\\s*-?\\s*pack\\b").find(name)

        val containerType = when {
            isLiquidL != null -> "${isLiquidL.groupValues[1]} litre bottle or jug"
            isLiquidMl != null -> "${isLiquidMl.groupValues[1]} millilitre bottle or carton"
            isWeightKg != null -> "${isWeightKg.groupValues[1]} kilogram package or bag"
            isWeightG != null -> "${isWeightG.groupValues[1]} gram package or loaf"
            isWeightMg != null -> "${isWeightMg.groupValues[1]} milligram box"
            isPack != null -> "${isPack.groupValues[1]} pack"
            else -> "retail product package"
        }

        return "commercial retail product photo of $name, $containerType, studio lighting, clean store background, 8k"
    }

    /**
     * Generates a real-time AI product image URL based on product name and measurement units.
     */
    fun generateAiImageUrl(productName: String, seed: Int = 1): String {
        val prompt = buildSmartPrompt(productName)
        val encodedPrompt = try {
            URLEncoder.encode(prompt, "UTF-8")
        } catch (_: Exception) {
            productName.replace(" ", "%20")
        }
        return "https://image.pollinations.ai/prompt/$encodedPrompt?width=400&height=300&seed=$seed&nologo=true"
    }
}
