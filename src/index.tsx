import React, { useEffect } from "react"
import { createRoot } from "react-dom/client"
import "./index.scss"
import { Button, TextareaControl } from "@wordpress/components"
import apiFetch from "@wordpress/api-fetch"

interface ISettings {
  waf_settings: {
    waf_text: string
  }
}

const Admin = (): JSX.Element => {
  const [wafText, setWafText] = React.useState("")

  useEffect(() => {
    const fetchData = async () => {
      const response: ISettings = await apiFetch({
        path: "/index.php?rest_route=/wp/v2/settings/",
      })
      setWafText(response.waf_settings?.waf_text)
    }

    fetchData()
  }, [])

  const onClick = async () => {
    try {
      await apiFetch({
        path: "/index.php?rest_route=/wp/v2/settings/",
        method: "POST",
        data: {
          waf_settings: {
            waf_text: wafText,
          },
        },
      })
      alert("保存しました")
    } catch (error) {
      console.error(error)
      alert("エラーが発生しました")
    }
  }

  return (
    <div>
      <h1>管理画面</h1>
      <TextareaControl
        label='テキスト'
        value={wafText}
        onChange={(value) => setWafText(value)}
      />
      <Button variant='primary' onClick={() => onClick()}>
        Save
      </Button>
    </div>
  )
}

const container = document.getElementById("wp-api-fetch")
if (container) {
  const root = createRoot(container)
  root.render(<Admin />)
}
